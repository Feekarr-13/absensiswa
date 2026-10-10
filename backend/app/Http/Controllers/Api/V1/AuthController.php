<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        // Mode SPA Sanctum membutuhkan sesi; permintaan dari origin yang tidak terdaftar tidak punya sesi.
        if (! $request->hasSession()) {
            return $this->error('Permintaan tidak berasal dari klien yang diizinkan.', 403);
        }

        $login = $request->string('login')->toString();
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // is_active ikut diperiksa: akun nonaktif mendapat pesan generik yang sama (tidak membocorkan status akun).
        $credentials = [$field => $login, 'password' => $request->input('password'), 'is_active' => true];

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            return $this->error('Email/username atau password salah.', 422, [
                'login' => ['Email/username atau password salah.'],
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();
        $user->forceFill(['last_login_at' => now()])->save();
        $user->load('student.classroom');

        return $this->success(new UserResource($user), 'Login berhasil.');
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->success(null, 'Logout berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('student.classroom');

        return $this->success(new UserResource($user));
    }
}