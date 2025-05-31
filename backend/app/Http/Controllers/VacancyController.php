<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\Career;
use Illuminate\Http\Request;
use App\Models\LogActivity; // Import model LogActivity
use Carbon\Carbon; // Untuk pencatatan waktu
use Illuminate\Support\Facades\Storage;

class VacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data vacancy
        $vacancies = Vacancy::with('career')->get();

        return response()->json($vacancies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'age' => 'required|integer',
            'career_id' => 'required|exists:careers,id', // Pastikan career_id valid
            'CV' => 'required|file|mimes:pdf|max:2048', // CV harus berupa PDF
            'Portofolio' => 'nullable|file|mimes:pdf|max:2048', // Portofolio juga berupa PDF, opsional
            'description' => 'nullable|string',
        ]);

        // Proses upload CV
        $cvPath = $request->file('CV')->store('cv', 'public');

        // Proses upload Portofolio jika ada
        $portofolioPath = null;
        if ($request->hasFile('Portofolio')) {
            $portofolioPath = $request->file('Portofolio')->store('portofolios', 'public');
        }

        // Buat data vacancy baru
        $vacancy = Vacancy::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'age' => $request->age,
            'career_id' => $request->career_id,
            'CV' => $cvPath,
            'Portofolio' => $portofolioPath,
            'description' => $request->description,
        ]);

        return response()->json($vacancy, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Tampilkan data vacancy berdasarkan id
        $vacancy = Vacancy::with('career')->findOrFail($id);

        return response()->json($vacancy);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'age' => 'required|integer',
            'career_id' => 'required|exists:careers,id', // Pastikan career_id valid
            'CV' => 'nullable|file|mimes:pdf|max:2048', // CV berupa PDF
            'Portofolio' => 'nullable|file|mimes:pdf|max:2048', // Portofolio berupa PDF, opsional
            'description' => 'nullable|string',
        ]);

        // Cari data vacancy berdasarkan id
        $vacancy = Vacancy::findOrFail($id);

        // Proses upload CV jika ada
        if ($request->hasFile('CV')) {
            // Hapus file CV lama
            if ($vacancy->CV) {
                Storage::disk('public')->delete($vacancy->CV);
            }
            $cvPath = $request->file('CV')->store('cv', 'public');
            $vacancy->CV = $cvPath;
        }

        // Proses upload Portofolio jika ada
        if ($request->hasFile('Portofolio')) {
            // Hapus file Portofolio lama
            if ($vacancy->Portofolio) {
                Storage::disk('public')->delete($vacancy->Portofolio);
            }
            $portofolioPath = $request->file('Portofolio')->store('portofolios', 'public');
            $vacancy->Portofolio = $portofolioPath;
        }

        // Update data lainnya
        $vacancy->full_name = $request->full_name;
        $vacancy->email = $request->email;
        $vacancy->phone = $request->phone;
        $vacancy->age = $request->age;
        $vacancy->career_id = $request->career_id;
        $vacancy->description = $request->description;
        $vacancy->save();

        return response()->json($vacancy);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Hapus data vacancy berdasarkan id
        $vacancy = Vacancy::findOrFail($id);

        // Hapus file CV jika ada
        if ($vacancy->CV) {
            Storage::disk('public')->delete($vacancy->CV);
        }

        // Hapus file Portofolio jika ada
        if ($vacancy->Portofolio) {
            Storage::disk('public')->delete($vacancy->Portofolio);
        }

        $vacancy->delete();

        return response()->json(null, 204);
    }


    /**
 * Download a file (CV or Portfolio) of a vacancy.
 */
public function downloadFile($id, $type)
{
    // Cari data vacancy berdasarkan ID
    $vacancy = Vacancy::findOrFail($id);

    // Tentukan file path berdasarkan tipe file
    $filePath = null;
    if ($type === 'cv') {
        $filePath = $vacancy->CV;
    } elseif ($type === 'portfolio') {
        $filePath = $vacancy->Portofolio;
    }

    // Jika file tidak ditemukan, kembalikan respons error
    if (!$filePath || !Storage::disk('public')->exists($filePath)) {
        return response()->json(['message' => 'File not found.'], 404);
    }

    // Kembalikan file sebagai response download
    return response()->download(storage_path("app/public/{$filePath}"));
}

 /**
 * Update the status of a vacancy to 'verified'.
 */
public function updateStatus(Request $request, $id)
{
    try {
        // Validasi input
        $request->validate([
            'admin_id' => 'required|exists:admins,id', // Pastikan admin_id valid
        ]);

        // Cari data vacancy berdasarkan ID
        $vacancy = Vacancy::findOrFail($id);

        // Perbarui status menjadi 'verified'
        $vacancy->status = 'verified';
        $vacancy->save();

        // Catat log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Update status verified with ID: ' . $vacancy->id,
        ]);

        // Kembalikan response sukses
        return response()->json([
            'message' => 'Status updated to verified.',
            'vacancy' => $vacancy,
        ], 200);
    } catch (\Exception $e) {
        // Log error untuk debug
        Log::error('Error updating vacancy status: ' . $e->getMessage());

        return response()->json([
            'error' => 'Failed to update vacancy status.',
            'message' => $e->getMessage(),
        ], 500);
    }
}


}
