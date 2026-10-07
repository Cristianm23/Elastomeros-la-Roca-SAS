<?php

namespace App\Modules\Quotes\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'message' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'items.*.description' => ['required_without:items.*.product_id', 'nullable', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
