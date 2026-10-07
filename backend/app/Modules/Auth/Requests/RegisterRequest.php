<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'company_name' => ['required', 'string', 'max:160'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'privacy_accepted' => ['required', 'accepted'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
