<?php
namespace App\Modules\Administration\Services;
use App\Modules\Administration\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
class AuditService
{
    public function record(string $action, ?Model $model, ?array $old, ?array $new, Request $request): void
    {
        AuditLog::create(['user_id' => $request->user()?->id, 'action' => $action, 'auditable_type' => $model ? $model::class : null, 'auditable_id' => $model?->getKey(), 'old_values' => $old, 'new_values' => $new, 'ip_address' => $request->ip()]);
    }
}
