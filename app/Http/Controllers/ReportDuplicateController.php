<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportDuplicate;
use Illuminate\Support\Facades\Auth;

class ReportDuplicateController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string|in:App\Models\Doctor,App\Models\Hospital',
            'reason' => 'nullable|string|max:500'
        ]);

        ReportDuplicate::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'reportable_id' => $validated['reportable_id'],
            'reportable_type' => $validated['reportable_type'],
            'reason' => $validated['reason'] ?? '',
        ]);

        return back()->with('success', 'Thank you. The profile has been reported as a duplicate and will be reviewed by our team.');
    }
}
