<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()->favorites()->with('favoriteable')->get();
        return view('user.favorites', compact('favorites'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:doctor,hospital'],
            'id'   => ['required', 'integer'],
        ]);

        $model = $request->type === 'doctor'
            ? Doctor::findOrFail($request->id)
            : Hospital::findOrFail($request->id);

        $existing = Favorite::where('user_id', auth()->id())
            ->where('favoriteable_type', get_class($model))
            ->where('favoriteable_id', $model->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        Favorite::create([
            'user_id'          => auth()->id(),
            'favoriteable_type' => get_class($model),
            'favoriteable_id'  => $model->id,
        ]);

        return response()->json(['saved' => true]);
    }

    public function check(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['saved' => false]);
        }

        $request->validate([
            'type' => ['required', 'in:doctor,hospital'],
            'id'   => ['required', 'integer'],
        ]);

        $model = $request->type === 'doctor'
            ? \App\Models\Doctor::findOrFail($request->id)
            : \App\Models\Hospital::findOrFail($request->id);

        $existing = Favorite::where('user_id', auth()->id())
            ->where('favoriteable_type', get_class($model))
            ->where('favoriteable_id', $model->id)
            ->exists();

        return response()->json(['saved' => $existing]);
    }
}
