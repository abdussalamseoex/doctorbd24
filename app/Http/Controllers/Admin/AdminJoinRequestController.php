<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JoinRequest;
use Illuminate\Http\Request;

class AdminJoinRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = JoinRequest::with(['division', 'district', 'area']);
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        $requests = $query->latest()->paginate(20)->withQueryString();
        return view('admin.join-requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, int $id, string $action)
    {
        $req = JoinRequest::findOrFail($id);

        $status = match($action) {
            'approve' => 'approved',
            'reject'  => 'rejected',
            default   => 'pending',
        };

        $req->update(['status' => $status]);

        return redirect()->back()->with('success', 'Request ' . $status . '.');
    }
}
