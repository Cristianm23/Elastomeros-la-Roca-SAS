<?php

namespace App\Modules\Administration\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Administration\Models\AuditLog;
use App\Modules\Administration\Models\BlogPost;
use App\Modules\Administration\Models\Faq;
use App\Modules\Administration\Models\Testimonial;
use App\Modules\Administration\Services\AuditService;
use App\Modules\Analytics\Models\AnalyticsEvent;
use App\Notifications\QuoteStatusChangedNotification;
use Illuminate\Support\Facades\Notification;
use App\Modules\Categories\Models\Category;
use App\Modules\Contact\Models\ContactMessage;
use App\Modules\Products\Models\Product;
use App\Modules\Quotes\Models\Quote;
use App\Modules\Quotes\Models\QuoteStatus;
use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function dashboard(): JsonResponse
    {
        return response()->json(['data' => [
            'quotes' => Quote::count(), 'pending_quotes' => Quote::where('status', QuoteStatus::PENDING)->count(),
            'contacts' => ContactMessage::count(), 'products' => Product::count(), 'users' => User::count(),
            'activity' => AuditLog::with('user')->latest()->limit(10)->get(),
        ]]);
    }

    public function quotes(Request $request): JsonResponse
    {
        $query = Quote::with(['items', 'assignee'])->latest();
        $query->when($request->string('search')->toString(), fn ($q, $value) => $q->where(fn ($q) => $q->where('public_reference', 'like', "%{$value}%")->orWhere('company_name', 'like', "%{$value}%")->orWhere('email', 'like', "%{$value}%")));
        $query->when($request->string('status')->toString(), fn ($q, $value) => $q->where('status', $value));
        return response()->json($query->paginate(20));
    }

    public function quote(Quote $quote): JsonResponse
    {
        return response()->json(['data' => $quote->load(['items', 'assignee', 'user'])]);
    }

    public function updateQuote(Request $request, Quote $quote): JsonResponse
    {
        $data = $request->validate(['status' => ['sometimes', 'in:pending,reviewing,quoted,accepted,rejected,completed,cancelled'], 'assigned_to' => ['nullable', 'exists:users,id'], 'admin_notes' => ['nullable', 'string', 'max:5000']]);
        $old = $quote->only(array_keys($data)); $quote->update($data); $this->audit->record('quote.updated', $quote, $old, $quote->only(array_keys($data)), $request);
        $oldStatus = $old['status'] instanceof QuoteStatus ? $old['status']->value : $old['status'];
        if ($quote->user && isset($data['status']) && $oldStatus !== $data['status']) {
            Notification::send($quote->user, new QuoteStatusChangedNotification($quote->fresh()));
        }
        return response()->json(['data' => $quote->fresh(['items', 'assignee'])]);
    }

    public function contacts(): JsonResponse { return response()->json(['data' => ContactMessage::latest()->paginate(20)]); }
    public function users(): JsonResponse { return response()->json(['data' => User::with('roles')->latest()->paginate(20)]); }
    public function roles(): JsonResponse { return response()->json(['data' => Role::with('permissions')->orderBy('name')->get(), 'permissions' => Permission::orderBy('slug')->get()]); }
    public function updateUserRoles(Request $request, User $user): JsonResponse
    {
        $data = $request->validate(['roles' => ['required', 'array'], 'roles.*' => ['exists:roles,id']]);
        $old = $user->roles()->pluck('roles.id')->all(); $user->roles()->sync($data['roles']);
        $this->audit->record('user.permissions_changed', $user, ['roles' => $old], ['roles' => $data['roles']], $request);
        return response()->json(['data' => $user->fresh('roles')]);
    }
    public function updateRolePermissions(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate(['permissions' => ['required', 'array'], 'permissions.*' => ['exists:permissions,id']]);
        $old = $role->permissions()->pluck('permissions.id')->all(); $role->permissions()->sync($data['permissions']);
        $this->audit->record('role.permissions_changed', $role, ['permissions' => $old], ['permissions' => $data['permissions']], $request);
        return response()->json(['data' => $role->fresh('permissions')]);
    }
    public function activity(): JsonResponse { return response()->json(['data' => AuditLog::with('user')->latest()->paginate(30)]); }

    public function analytics(Request $request): JsonResponse
    {
        $since = $request->date('since')?->startOfDay() ?? now()->subDays(30);
        $events = AnalyticsEvent::query()->where('created_at', '>=', $since);

        return response()->json(['data' => [
            'since' => $since->toDateString(),
            'totals' => (clone $events)->selectRaw('event_name, count(*) as total')->groupBy('event_name')->orderByDesc('total')->get(),
            'products' => (clone $events)->where('event_name', 'product_view')->whereNotNull('product_slug')->selectRaw('product_slug, count(*) as total')->groupBy('product_slug')->orderByDesc('total')->limit(20)->get(),
            'recent' => (clone $events)->latest()->limit(50)->get(['event_name', 'path', 'product_slug', 'properties', 'created_at']),
        ]]);
    }

    public function content(Request $request, string $type): JsonResponse
    {
        $model = $this->contentModel($type);
        return response()->json(['data' => $model::latest()->paginate(20)]);
    }

    public function saveContent(Request $request, string $type): JsonResponse
    {
        $model = $this->contentModel($type);
        $rules = match ($type) {
            'faqs' => ['question' => ['required', 'string', 'max:255'], 'answer' => ['required', 'string'], 'is_active' => ['boolean']],
            'testimonials' => ['author' => ['required', 'string', 'max:160'], 'company' => ['nullable', 'string', 'max:160'], 'quote' => ['required', 'string'], 'is_active' => ['boolean']],
            'blog' => ['title' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255'], 'excerpt' => ['nullable', 'string'], 'body' => ['required', 'string'], 'status' => ['required', 'in:draft,published']],
            default => abort(404),
        };
        $data = $request->validate($rules); if ($type === 'blog') $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $item = $request->integer('id') ? $model::findOrFail($request->integer('id')) : new $model;
        $old = $item->exists ? $item->toArray() : null; $item->fill($data); if ($type === 'blog') $item->author_id = $request->user()->id; $item->save();
        $this->audit->record($old ? 'content.updated' : 'content.created', $item, $old, $item->toArray(), $request);
        return response()->json(['data' => $item], $old ? 200 : 201);
    }

    public function deleteContent(Request $request, string $type, int $id): JsonResponse
    {
        $model = $this->contentModel($type); $item = $model::findOrFail($id); $old = $item->toArray(); $item->delete(); $this->audit->record('content.deleted', $item, $old, null, $request);
        return response()->json([], 204);
    }

    private function contentModel(string $type): string
    {
        return match ($type) { 'faqs' => Faq::class, 'testimonials' => Testimonial::class, 'blog' => BlogPost::class, default => abort(404) };
    }
}
