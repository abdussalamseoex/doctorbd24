<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ContactMessage;

class AdminContactMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $query = ContactMessage::query();

        if ($status && in_array($status, ['pending', 'replied', 'resolved'])) {
            $query->where('status', $status);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $counts = [
            'total' => ContactMessage::count(),
            'pending' => ContactMessage::where('status', 'pending')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count() + ContactMessage::where('status', 'resolved')->count(),
        ];

        return view('admin.contact-messages.index', compact('messages', 'counts'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $message = ContactMessage::findOrFail($id);
        
        if ($message->status === 'pending') {
            // Unofficial mark as read or just let admin manually do it.
        }

        return view('admin.contact-messages.show', compact('message'));
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, string $id)
    {
        $message = ContactMessage::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,replied,resolved'
        ]);

        $updateData = ['status' => $validated['status']];
        
        if (in_array($validated['status'], ['replied', 'resolved']) && !$message->replied_at) {
            $updateData['replied_at'] = now();
        }

        $message->update($updateData);

        return redirect()->back()->with('success', 'Message status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.contact-messages.index')->with('success', 'Message deleted successfully.');
    }
}
