<?php

namespace App\Http\Controllers;

use App\Models\KeywordBlog;
use App\Models\LogActivity; // Import LogActivity model
use Illuminate\Http\Request;
use Carbon\Carbon; // Import Carbon untuk pencatatan waktu

class KeywordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keywords = KeywordBlog::all();
        return response()->json($keywords);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_keyword' => 'required|string|max:255',
            'admin_id' => 'required|integer', // Validasi admin_id
        ]);

        // Buat keyword baru
        $keyword = KeywordBlog::create([
            'name_keyword' => $request->name_keyword,
        ]);

        // Log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new category-blog with ID:: ' . $keyword->id,
        ]);

        return response()->json($keyword, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $keyword = KeywordBlog::with('blogs')->findOrFail($id);

        return response()->json($keyword);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_keyword' => 'required|string|max:255',
            'admin_id' => 'required|integer', // Validasi admin_id
        ]);

        $keyword = KeywordBlog::findOrFail($id);
        $oldKeywordName = $keyword->name_keyword;

        $keyword->update([
            'name_keyword' => $request->name_keyword,
        ]);

        // Log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated category-blog with ID:: ' . $keyword->id,
        ]);

        return response()->json($keyword);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'admin_id' => 'required|integer', // Validasi admin_id
        ]);

        $keyword = KeywordBlog::findOrFail($id);
        $deletedKeywordName = $keyword->name_keyword;

        $keyword->delete();

        // Log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Deleted',
            'description' => 'Deleted category-blog with ID:: ' . $keyword->id,
        ]);

        return response()->json(null, 200);
    }
}
