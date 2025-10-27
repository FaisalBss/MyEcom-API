<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function verify(Request $request, $id, $hash)
    {
        $result = $this->authService->verifyEmail($id, $hash);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ], $result['status']);
    }
}
