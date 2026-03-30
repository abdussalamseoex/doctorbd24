<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClaimRequest;
use Spatie\Permission\Models\Role;

class AdminClaimRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = ClaimRequest::with(['user', 'doctor', 'hospital', 'ambulance'])->latest();
        
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(15);
        return view('admin.claim-requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, ClaimRequest $claimRequest)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);

        $roleName = $claimRequest->ambulance_id ? 'ambulance' : ($claimRequest->hospital_id ? 'hospital' : 'doctor');
        $entity = $claimRequest->ambulance_id ? $claimRequest->ambulance : ($claimRequest->hospital_id ? $claimRequest->hospital : $claimRequest->doctor);
        $foreignColumn = $claimRequest->ambulance_id ? 'ambulance_id' : ($claimRequest->hospital_id ? 'hospital_id' : 'doctor_id');

        if ($request->status === 'approved') {
            // Update ownership
            if ($entity) $entity->update(['user_id' => $claimRequest->user_id]);

            // Assign role to user
            $user = $claimRequest->user;
            if (!$user->hasRole($roleName)) {
                if(Role::where('name', $roleName)->doesntExist()){
                    Role::create(['name' => $roleName]);
                }
                $user->assignRole($roleName);
            }

            // Reject competing claims
            if ($entity) {
                ClaimRequest::where($foreignColumn, $entity->id)
                    ->where('id', '!=', $claimRequest->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);
            }
        } elseif ($request->status === 'rejected') {
            // If reverting an approved claim
            if ($claimRequest->status === 'approved' && $entity) {
                 if ($entity->user_id == $claimRequest->user_id) {
                     $entity->update(['user_id' => null]);
                 }
            }
        }

        $claimRequest->update(['status' => $request->status]);

        return back()->with('success', 'Claim request status updated successfully.');
    }
}
