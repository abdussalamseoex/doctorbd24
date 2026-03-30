<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Setting;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'    => ['required', 'in:doctor,hospital'],
            'id'      => ['required', 'integer'],
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        if (auth()->user()->hasAnyRole(['admin', 'doctor', 'hospital', 'editor', 'moderator'])) {
            return back()->with('error', 'অ্যাডমিন অথবা প্রোভাইডাররা রেটিং দিতে পারবেন না।');
        }

        $model = $validated['type'] === 'doctor'
            ? Doctor::findOrFail($validated['id'])
            : Hospital::findOrFail($validated['id']);

        // Prevent duplicate review
        $existing = Review::where('user_id', auth()->id())
            ->where('reviewable_type', get_class($model))
            ->where('reviewable_id', $model->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'আপনি ইতিমধ্যে একটি রিভিউ দিয়েছেন।');
        }

        $autoApprove = Setting::get('review_auto_approve', '0') === '1';

        $model->reviews()->create([
            'user_id'     => auth()->id(),
            'rating'      => $validated['rating'],
            'comment'     => $validated['comment'] ?? null,
            'approved_at' => $autoApprove ? now() : null,
        ]);

        $message = $autoApprove
            ? 'আপনার রিভিউ সফলভাবে প্রকাশিত হয়েছে।'
            : 'আপনার রিভিউ পাঠানো হয়েছে। অ্যাডমিন অনুমোদনের পরে প্রকাশিত হবে।';

        return back()->with('success', $message);
    }

    public function update(Request $request, Review $review)
    {
        if (auth()->id() !== $review->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $autoApprove = Setting::get('review_auto_approve', '0') === '1';

        $review->update([
            'rating'      => $validated['rating'],
            'comment'     => $validated['comment'] ?? null,
            'approved_at' => $autoApprove ? now() : null,
        ]);

        $message = $autoApprove
            ? 'আপনার রিভিউ সফলভাবে আপডেট হয়েছে।'
            : 'রিভিউ আপডেট করা হয়েছে। অ্যাডমিন অনুমোদনের পরে প্রকাশিত হবে।';

        return back()->with('success', $message);
    }

    public function destroy(Review $review)
    {
        if (auth()->id() !== $review->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return back()->with('success', 'আপনার রিভিউ সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
