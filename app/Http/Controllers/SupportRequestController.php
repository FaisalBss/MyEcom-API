<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use App\Http\Requests\StoreSupportRequest;
use App\Http\Requests\AdminUpdateSupportRequest;
use App\Services\SupportService;
use Illuminate\Pagination\Paginator;

class SupportRequestController extends Controller
{
    public function __construct(private SupportService $supportService)
    {
        $this->middleware('auth:api');
        Paginator::useBootstrapFive();
    }

    public function index()
    {
        return view('contact.index');
    }

    public function previous()
    {
        $requests = auth()->user()
            ->supportRequests()
            ->latest()
            ->paginate(10);

        return view('contact.previous', compact('requests'));
    }

    public function create()
    {
        return view('contact.new');
    }

    public function store(StoreSupportRequest $request)
    {
        $this->supportService->createRequest($request->user(), $request->validated());

        return redirect()
            ->route('contact.previous')
            ->with('success', 'we sent your request Waiting for review');
    }

    public function adminIndex()
    {
        $requests = $this->supportService->getAllRequests(request('status'));
        return view('admin.support', compact('requests'));
    }

    public function adminShow($id)
    {
        $req = $this->supportService->getRequestWithDetails($id);
        return view('admin.show', compact('req'));
    }

    public function adminUpdateStatus(AdminUpdateSupportRequest $request, $id)
    {
        $this->supportService->updateStatus($id, $request->validated());

        return back()->with('success', 'Updated successfully');
    }
}
