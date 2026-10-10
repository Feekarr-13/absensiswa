<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SEMENTARA (Tahap 2): hanya membuktikan RBAC bekerja.
 * Diganti data nyata dari database pada Tahap 3 (siswa/orang tua) dan Tahap 5 (guru/admin).
 */
class DashboardController extends Controller
{
    public function student(Request $request): JsonResponse { return $this->probe($request, 'student'); }
    public function teacher(Request $request): JsonResponse { return $this->probe($request, 'teacher'); }
    public function parent(Request $request): JsonResponse { return $this->probe($request, 'parent'); }
    public function admin(Request $request): JsonResponse { return $this->probe($request, 'admin'); }

    private function probe(Request $request, string $area): JsonResponse
    {
        return $this->success(['area' => $area, 'user' => $request->user()->name], 'Placeholder dashboard.');
    }
}