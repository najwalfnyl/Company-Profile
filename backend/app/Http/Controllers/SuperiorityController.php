<?php

namespace App\Http\Controllers;

use App\Models\Superiority; // Use the Superiority model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuperiorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data superioritas
        $superiorities = Superiority::all();

        // Menambahkan URL lengkap untuk logo jika ada
        $superiorities->transform(function ($superiority) {
            if ($superiority->logo_superiority) {
                $superiority->logo_superiority = Storage::disk('public')->url($superiority->logo_superiority);
            }
            return $superiority;
        });

        return response()->json($superiorities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'logo_superiority' => 'nullable|file|mimes:jpg,png,jpeg|max:2048', // Validasi file logo
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Proses file upload
        $logoPath = null;
        if ($request->hasFile('logo_superiority')) {
            $logoPath = $request->file('logo_superiority')->store('logo_superiority', 'public');
        }

        // Buat data superioritas baru
        $superiority = Superiority::create([
            'logo_superiority' => $logoPath,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Ubah path menjadi URL lengkap
        $superiority->logo_superiority = $logoPath ? Storage::disk('public')->url($logoPath) : null;

        return response()->json($superiority, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Tampilkan data superioritas berdasarkan id
        $superiority = Superiority::findOrFail($id);

        // Tambahkan URL lengkap untuk logo jika ada
        if ($superiority->logo_superiority) {
            $superiority->logo_superiority = Storage::disk('public')->url($superiority->logo_superiority);
        }

        return response()->json($superiority);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'logo_superiority' => 'nullable|file|mimes:jpg,png,jpeg|max:2048', // Validasi file logo
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Cari data superioritas berdasarkan id
        $superiority = Superiority::findOrFail($id);

        // Proses file upload jika ada
        if ($request->hasFile('logo_superiority')) {
            // Hapus file logo lama jika ada
            if ($superiority->logo_superiority) {
                Storage::disk('public')->delete($superiority->logo_superiority);
            }
            $logoPath = $request->file('logo_superiority')->store('logo_superiority', 'public');
            $superiority->logo_superiority = $logoPath;
        }

        // Update data lainnya
        $superiority->name = $request->name;
        $superiority->description = $request->description;
        $superiority->save();

        // Ubah path menjadi URL lengkap
        $superiority->logo_superiority = $superiority->logo_superiority ? Storage::disk('public')->url($superiority->logo_superiority) : null;

        return response()->json($superiority);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Hapus data superioritas berdasarkan id
        $superiority = Superiority::findOrFail($id);

        // Hapus file logo jika ada
        if ($superiority->logo_superiority) {
            Storage::disk('public')->delete($superiority->logo_superiority);
        }

        $superiority->delete();

        return response()->json(null, 204);
    }
}
