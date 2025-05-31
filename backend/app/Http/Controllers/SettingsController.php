<?php
namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SettingsController extends Controller
{
    // Menampilkan semua settings (tanpa log)
   // Menampilkan setting dengan ID paling terakhir
public function index()
{
    $setting = Settings::all();
    return response()->json($setting);
}


    // Menyimpan setting baru
    public function store(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                'admin_id' => 'required|integer',
                'description1' => 'required|string|max:255',
                'description2' => 'required|string|max:255',
                'year' => 'required|integer',
                'email' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'instagram' => 'nullable|string|max:255',
                'facebook' => 'nullable|string|max:255',
                'linkedln' => 'nullable|string|max:255',
            ]);
    
            // Simpan data ke dalam database
            $settings = Settings::create([
                'description1' => $request->description1,
                'description2' => $request->description2,
                'year' => $request->year,
                'email' => $request->email,
                'phone' => $request->phone,
                'instagram' => $request->instagram,
                'facebook' => $request->facebook,
                'linkedln' => $request->linkedln,
            ]);
    
            // Log aktivitas untuk pembuatan setting baru
            LogActivity::create([
                'admin_id' => $request->admin_id,
                'date' => Carbon::now('Asia/Jakarta')->toDateString(),
                'time' => Carbon::now('Asia/Jakarta')->format('H:i'), // Waktu Jakarta dengan format HH:mm
                'action' => 'Added',
                'description' => 'Added new setting with ID : ' . $settings->id,
            ]);
    
            return response()->json($settings, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Menampilkan setting berdasarkan id (tanpa log)
    public function show($id)
    {
        $settings = Settings::find($id);

        if (!$settings) {
            return response()->json(['message' => 'Setting not found'], 404);
        }

        return response()->json($settings);
    }

    // Mengupdate setting
    public function update(Request $request, $id)
    {
        $settings = Settings::find($id);
        if (!$settings) {
            return response()->json(['message' => 'Setting not found'], 404);
        }

        // Validasi request
        $request->validate([
            'admin_id' => 'required|integer', // Tambahkan validasi untuk admin_id
            'description1' => 'required|string|max:255',
            'description2' => 'required|string|max:255',
            'year' => 'required|integer',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'linkedln' => 'nullable|string|max:255',
        ]);

        // Update data lainnya
        $settings->description1 = $request->description1;
        $settings->description2 = $request->description2;
        $settings->year = $request->year;
        $settings->email = $request->email;
        $settings->phone = $request->phone;
        $settings->instagram = $request->instagram;
        $settings->facebook = $request->facebook;
        $settings->linkedln = $request->linkedln;
        $settings->save();

        // Log aktivitas untuk pembaruan setting
        LogActivity::create([
            'admin_id' => $request->admin_id, // Menggunakan admin_id dari request
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'), // Waktu Jakarta dengan format HH:mm
            'action' => 'Updated',
            'description' => 'Updated setting with ID: ' . $id,
        ]);

        return response()->json($settings, 200);
    }

    // Menghapus setting
    public function destroy(Request $request, $id)
    {
        $settings = Settings::find($id);
        if ($settings) {
            $settings->delete();

            // Log aktivitas untuk penghapusan setting
            LogActivity::create([
                'admin_id' => $request->admin_id, // Menggunakan admin_id dari request
                'date' => Carbon::now('Asia/Jakarta')->toDateString(),
                'time' => Carbon::now('Asia/Jakarta')->format('H:i'), // Waktu Jakarta dengan format HH:mm
                'action' => 'Deleted',
                'description' => 'Deleted setting with ID: ' . $id,
            ]);

            return response()->json(['message' => 'Setting deleted successfully'], 200);
        }

        return response()->json(['message' => 'Setting not found'], 404);
    }
}
