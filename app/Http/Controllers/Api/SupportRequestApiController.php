<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportRequest;
use App\Http\Requests\AdminUpdateSupportRequest;
use App\Services\SupportService;

class SupportRequestApiController extends Controller
{
    public function __construct(private SupportService $supportService)
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $requests = auth()->user()
            ->supportRequests()
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    public function store(StoreSupportRequest $request)
    {
        $support = $this->supportService->createRequest($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Support request submitted successfully. Waiting for review.',
            'data' => $support
        ], 201);
    }

    // الإدمن فقط
    public function adminIndex()
    {
        $this->authorizeAdmin();

        $requests = $this->supportService->getAllRequests(request('status'));

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    public function adminShow($id)
    {
        $this->authorizeAdmin();

        $req = $this->supportService->getRequestWithDetails($id);

        if (!$req) {
            return response()->json([
                'success' => false,
                'message' => 'Support request not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $req
        ]);
    }

    public function adminUpdateStatus(AdminUpdateSupportRequest $request, $id)
    {
        $this->authorizeAdmin();

        $updated = $this->supportService->updateStatus($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Support request updated successfully.',
            'data' => $updated
        ]);
    }

    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(response()->json([
                'success' => false,
                'message' => 'Unauthorized — Admin access only.'
            ], 403));
        }
    }
}
