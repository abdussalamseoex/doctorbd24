<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RedirectLog;

class RedirectLogController extends Controller
{
    public function index()
    {
        $logs = RedirectLog::orderBy('last_hit_at', 'desc')->paginate(50);
        return view('admin.redirect_logs.index', compact('logs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_url' => 'required|string',
            'to_url' => 'required|string',
        ]);

        RedirectLog::firstOrCreate(
            ['from_url' => $request->from_url],
            [
                'to_url' => $request->to_url,
                'hits' => 0,
                'last_hit_at' => now(),
            ]
        )->update(['to_url' => $request->to_url]);

        return redirect()->back()->with('success', 'Manual redirect added successfully.');
    }

    public function destroy(RedirectLog $redirectLog)
    {
        $redirectLog->delete();
        return redirect()->back()->with('success', 'Redirect log deleted successfully.');
    }
}
