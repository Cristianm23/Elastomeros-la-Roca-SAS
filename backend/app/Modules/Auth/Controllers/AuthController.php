<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use App\Modules\Users\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $user = \App\Models\User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! $user->is_active || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['Las credenciales no son válidas.']]);
        }

        $expiresAt = ($credentials['remember'] ?? false)
            ? now()->addDays(30)
            : now()->addHours(2);
        $token = $user->createToken($credentials['device_name'] ?? 'api-client', ['*'], $expiresAt)->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            ],
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data): \App\Models\User {
            $user = \App\Models\User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company_name' => $data['company_name'],
                'password' => $data['password'],
                'privacy_accepted_at' => now(),
            ]);
            $user->roles()->attach(Role::query()->where('slug', 'customer')->value('id'));
            return $user;
        });

        $token = $user->createToken($data['device_name'] ?? 'api-client', ['*'], now()->addHours(2))->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            ],
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(['data' => ['message' => 'Sesión cerrada.']]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        Password::sendResetLink($request->validated());

        return response()->json(['data' => ['message' => 'Si el correo existe, recibirás instrucciones para restablecer tu contraseña.']]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $status = Password::reset($data, function (\App\Models\User $user, string $password): void {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            $user->tokens()->delete();
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => ['El enlace de recuperación no es válido o ha expirado.']]);
        }

        return response()->json(['data' => ['message' => 'Contraseña actualizada correctamente.']]);
    }
}
