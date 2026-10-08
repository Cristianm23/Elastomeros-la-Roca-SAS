<?php

namespace App\Modules\Analytics\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnalyticsEventRequest extends FormRequest
{
    private const EVENT_NAMES = [
        'page_view', 'product_view', 'search', 'quote_started', 'quote_submitted',
        'contact_submitted', 'login', 'register', 'cta_click', 'whatsapp_click',
        'phone_click', 'pdf_download', 'frontend_error', 'api_failure',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_name' => ['required', 'string', Rule::in(self::EVENT_NAMES)],
            'path' => ['nullable', 'string', 'max:2048'],
            'product_slug' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/'],
            'properties' => ['nullable', 'array', 'max:10'],
            'properties.*' => ['nullable', 'string', 'max:160'],
        ];
    }
}
