<?php

namespace App\Modules\Quotes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:160'],
            'contact_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:120'],
            'required_date' => ['required', 'date', 'after_or_equal:today'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'consent' => ['required', 'accepted'],
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', File::types(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'webp'])->max(10 * 1024)],
            'website' => ['nullable', 'max:0'],
            'product_slug' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'items.*.description' => ['required_without:items.*.product_id', 'nullable', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
