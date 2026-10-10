<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->deny('Silakan login terlebih dahulu.', 401);
        }

        if (! $user->is_active || ! in_array($user->role->value, $roles, true)) {
            return $this->deny('Anda tidak memiliki akses ke sumber daya ini.', 403);
        }

        return $next($request);
    }

    private function deny(string $message, int $status): Response
    {
        return response()->json(['success' => false, 'message' => $message, 'errors' => (object) []], $status);
    }
}