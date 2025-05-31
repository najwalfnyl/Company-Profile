<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'admin_id' => 'required|exists:admins,id',
        ]);

        // Buat tag baru
        $tag = Tag::create([
            'name' => $request->name,
        ]);

        // Catat log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new tag with ID: ' . $tag->id,
        ]);

        return response()->json($tag, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tag = Tag::findOrFail($id);
        return response()->json($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'admin_id' => 'required|exists:admins,id',
        ]);

        $tag = Tag::findOrFail($id);

        // Update tag
        $tag->update([
            'name' => $request->name,
        ]);

        // Catat log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated tag with ID: ' . $id,
        ]);

        return response()->json($tag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $tag->delete();

        // Catat log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Deleted',
            'description' => 'Deleted tag with ID: ' . $id,
        ]);

        return response()->json([
            'message' => 'Tag deleted successfully',
        ], 200);
    }
}
