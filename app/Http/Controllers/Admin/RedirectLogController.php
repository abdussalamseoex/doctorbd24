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

    public function destroy(RedirectLog $redirectLog)
    {
        $redirectLog->delete();
        return redirect()->back()->with('success', 'Redirect log deleted successfully.');
    }
}
