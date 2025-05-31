<?php


namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\LogActivity;
use Carbon\Carbon;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data perusahaan
        $perusahaans = Perusahaan::all();


        // Menambahkan URL lengkap untuk logo jika ada
        $perusahaans->transform(function ($perusahaan) {
            if ($perusahaan->logo) {
                $perusahaan->logo = Storage::disk('public')->url($perusahaan->logo);
            }
            return $perusahaan;
        });


        return response()->json($perusahaans);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'logo' => 'nullable|file|mimes:jpg,png,jpeg|max:2048', // Validasi file logo
           'testimony' => 'nullable|regex:/^[a-zA-Z\s]*$/',
            'nama_client' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'admin_id' => 'required|integer', // Pastikan admin_id ada
        ]);
   
        // Proses file upload dengan nama file yang unik
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $logoPath = $file->storeAs('logos', $filename, 'public');
        }
   
        // Buat data perusahaan baru
        $perusahaan = Perusahaan::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'logo' => $logoPath,
            'testimony' => $request->testimony,
            'nama_client' => $request->nama_client,
            'role' => $request->role,
        ]);
   
        // Ubah path menjadi URL lengkap
        $perusahaan->logo = $logoPath ? Storage::disk('public')->url($logoPath) : null;
   
        // Simpan log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new client: ' . $perusahaan->id,
        ]);
   
        return response()->json($perusahaan, 201);
    }
   


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Tampilkan data perusahaan berdasarkan id
        $perusahaan = Perusahaan::findOrFail($id);


        // Tambahkan URL lengkap untuk logo jika ada
        if ($perusahaan->logo) {
            $perusahaan->logo = Storage::disk('public')->url($perusahaan->logo);
        }


        return response()->json($perusahaan);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'logo' => 'nullable|file|mimes:jpg,png,jpeg|max:2048', // Validasi file logo
            'testimony' => 'nullable|regex:/^[a-zA-Z\s]*$/',
            'nama_client' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
        ]);


        // Cari data perusahaan berdasarkan id
        $perusahaan = Perusahaan::findOrFail($id);


        // Proses file upload jika ada dan buat nama unik
        if ($request->hasFile('logo')) {
            // Hapus file logo lama jika ada
            if ($perusahaan->logo) {
                Storage::disk('public')->delete($perusahaan->logo);
            }
            // Generate a unique filename using uniqid()
            $file = $request->file('logo');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            // Store the file with the unique name
            $logoPath = $file->storeAs('logos', $filename, 'public');
            // Update logo path in the database
            $perusahaan->logo = $logoPath;
        }


        // Update data lainnya
        $perusahaan->nama_perusahaan = $request->nama_perusahaan;
        $perusahaan->testimony = $request->testimony;
        $perusahaan->nama_client = $request->nama_client;
        $perusahaan->role = $request->role;
        $perusahaan->save();


        // Ubah path menjadi URL lengkap
        $perusahaan->logo = $perusahaan->logo ? Storage::disk('public')->url($perusahaan->logo) : null;
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'), // Waktu Jakarta dengan format HH:mm
            'action' => 'Updated',
            'description' => 'Updated client with ID: ' . $id,
        ]);


        return response()->json($perusahaan);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
{
    try {
        // Hapus data perusahaan berdasarkan id
        $perusahaan = Perusahaan::findOrFail($id);


        // Hapus file logo jika ada
        if ($perusahaan->logo) {
            Storage::disk('public')->delete($perusahaan->logo);
        }


        $perusahaan->delete();


        // Buat log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'), // Waktu Jakarta dengan format HH:mm
            'action' => 'Deleted',
            'description' => 'Deleted client with ID: ' . $id,
        ]);


        return response()->json(null, 204);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to delete client', 'message' => $e->getMessage()], 500);
    }
}


}
