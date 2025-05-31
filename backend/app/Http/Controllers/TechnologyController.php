<?php

namespace App\Http\Controllers;

use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\LogActivity;
use Carbon\Carbon;


class TechnologyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data technologies beserta relasi category
        $technologies = Technology::with('category')->get()->map(function($technology) {
            // Tambahkan URL lengkap untuk logo
            if ($technology->logo) {
                $technology->logo_url = Storage::url($technology->logo);
            } else {
                $technology->logo_url = null;
            }
            return $technology;
        });

        return response()->json($technologies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'name_technology' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'technology_category_id' => 'required|exists:technology_categories,id',
        ]);
    
        // Handle file upload for the logo with unique filenames
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            // Generate a unique filename
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            // Store the file with the unique name
            $logoPath = $file->storeAs('logos_technology', $filename, 'public');
        }
    
        // Create new technology record
        $technology = Technology::create([
            'name_technology' => $request->name_technology,
            'logo' => $logoPath,
            'technology_category_id' => $request->technology_category_id,
        ]);
    
        // Log activity for creation
        LogActivity::create([
            'admin_id' => $request->admin_id,  // Assuming the admin ID is passed in the request
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new technology with ID:: ' . $technology->id,
        ]);
    
        return response()->json($technology, 201);
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Tampilkan data technology berdasarkan id beserta kategori
        $technology = Technology::with('category')->findOrFail($id);

        // Menambahkan URL lengkap untuk logo
        if ($technology->logo) {
            $technology->logo_url = Storage::url($technology->logo);
        } else {
            $technology->logo_url = null;
        }

        return response()->json($technology);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    // Validasi data yang dikirimkan
    $request->validate([
        'name_technology' => 'required|string|max:255',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'technology_category_id' => 'required|exists:technology_categories,id',
    ]);

    // Cari data technology berdasarkan id
    $technology = Technology::findOrFail($id);

    // Handle file upload for the logo with unique filenames
    if ($request->hasFile('logo')) {
        // Delete old logo if exists
        if ($technology->logo) {
            Storage::disk('public')->delete($technology->logo);
        }

        // Store the new logo with a unique filename
        $file = $request->file('logo');
        $filename = uniqid() . '_' . $file->getClientOriginalName();
        $technology->logo = $file->storeAs('logos_technology', $filename, 'public');
    }

    // Update technology record
    $technology->update([
        'name_technology' => $request->name_technology,
        'logo' => $technology->logo,
        'technology_category_id' => $request->technology_category_id,
    ]);

    // Log activity for update
    LogActivity::create([
        'admin_id' => $request->admin_id,  // Assuming the admin ID is passed in the request
        'date' => Carbon::now('Asia/Jakarta')->toDateString(),
        'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
        'action' => 'Updated',
        'description' => 'Updated technology with ID:: ' . $id,
    ]);

    return response()->json($technology);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Hapus data technology berdasarkan id
            $technology = Technology::findOrFail($id);
    
            // Delete logo if exists
            if ($technology->logo) {
                Storage::disk('public')->delete($technology->logo);
            }
    
            $technology->delete();
    
            // Log activity for deletion
            LogActivity::create([
                'admin_id' => $request->admin_id,  // Assuming the admin ID is passed in the request
                'date' => Carbon::now('Asia/Jakarta')->toDateString(),
                'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
                'action' => 'Deleted',
                'description' => 'Deleted technology with ID: ' . $id,
            ]);
    
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
}
