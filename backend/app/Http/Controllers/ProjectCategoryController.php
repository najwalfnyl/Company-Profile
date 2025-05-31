<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data project categories
        $categories = ProjectCategory::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'name_category' => 'required|string|max:255',
            'admin_id' => 'required|integer',
        ]);

        // Buat data project category baru
        $categories = new ProjectCategory();
        $categories->name_category = $request->name_category;
        $categories->save();

        // Log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new project category with ID:: ' . $categories->id_category,
        ]);

        return response()->json($categories, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_category)
    {
        // Tampilkan data project category berdasarkan id
        $categories = ProjectCategory::findOrFail($id_category);
        return response()->json($categories);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_category)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'name_category' => 'required|string|max:255',
            'admin_id' => 'required|integer',
        ]);

        // Cari data project category berdasarkan id
        $categories = ProjectCategory::findOrFail($id_category);

        // Simpan nama kategori lama untuk log
        $oldCategoryName = $categories->name_category;

        // Update data project category
        $categories->name_category = $request->name_category;
        $categories->save();

        // Log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated project category with ID:: ' . $categories->id_category,
        ]);

        return response()->json($categories);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id_category)
    {
        // Validasi admin_id
        $request->validate([
            'admin_id' => 'required|integer',
        ]);

        // Hapus data project category berdasarkan id
        $categories = ProjectCategory::findOrFail($id_category);

        // Simpan nama kategori untuk log
        $deletedCategoryName = $categories->name_category;

        $categories->delete();

        // Log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Deleted',
            'description' => 'Deleted project category with ID:: ' . $categories->id_category,
        ]);

        return response()->json(null, 204);
    }
}
