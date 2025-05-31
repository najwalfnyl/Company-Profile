<?php

namespace App\Http\Controllers;

use App\Models\Admin; // Pastikan model Admin diimport
use App\Models\LogActivity; // Pastikan model LogActivity diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class AdminController extends Controller
{
    /**
     * Register a new admin.
     */
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|string', // Tambahkan validasi untuk photo jika dibutuhkan
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Buat admin baru dengan nilai photo null jika tidak ada
        $admin = Admin::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'photo' => $request->photo ?? null, // Nilai photo akan menjadi null jika tidak ada input
        ]);
    
        return response()->json([
            'message' => 'Admin registered successfully',
            'admin' => $admin,
        ], 201);
    }
    

    /**
     * Admin login using email and password.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('auth_token')->plainTextToken;

        // Log aktivitas untuk login
        LogActivity::create([
            'admin_id' => $admin->id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'), // Format HH:mm
            'action' => 'Logged In',
            'description' => 'Admin logged in with ID: ' . $admin->id,
        ]);

        return response()->json([
            'message' => 'Login successful',
            'admin' => $admin,
            'token' => $token,
        ], 200);
    }

    /**
     * Admin logout.
     */
    public function logout(Request $request)
    {
        $admin = $request->user(); // Mendapatkan admin yang sedang login
    
        // Log aktivitas untuk logout
        LogActivity::create([
            'admin_id' => $admin->id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'), // Format HH:mm
            'action' => 'Logged Out',
            'description' => 'Admin logged out with ID: ' . $admin->id,
        ]);
    
        $request->user()->currentAccessToken()->delete();
    
        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
    
    /**
     * Get the profile of the logged-in admin.
     */
    public function profile(Request $request)
    {
        // Ambil admin yang sedang login
        $admin = $request->user();
    
        // Format respons JSON
        return response()->json([
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'username' => $admin->username,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'photo' => $admin->photo ? Storage::url($admin->photo) : null,
            ]
        ], 200);
    }
    

    public function all()
    {
        // Hanya mengambil kolom 'name'
        $names = Admin::select('name')->get()->pluck('name');
    
        return response()->json([
            'names' => $names
        ], 200);
    }
    
    
    /**
     * Edit or create the profile of the logged-in admin.
     */
    public function store(Request $request)
    {
        $admin = $request->user();
    
        // Validasi input untuk name, phone, dan photo
        $request->validate([
            'name' => 'sometimes|required|string|max:255',  // Menggunakan sometimes untuk opsional
            'phone' => 'sometimes|nullable|string|max:15',
            'photo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $changes = [];
        $oldPhoto = null;
    
        // Perbarui data yang ada dalam request tanpa memeriksa field lain
        if ($request->has('name') && $admin->name !== $request->input('name')) {
            $changes['name'] = ['old' => $admin->name, 'new' => $request->input('name')];
            $admin->name = $request->input('name');
        }
        if ($request->has('phone') && $admin->phone !== $request->input('phone')) {
            $changes['phone'] = ['old' => $admin->phone, 'new' => $request->input('phone')];
            $admin->phone = $request->input('phone');
        }
    
        // Perbarui photo jika ada dalam request
        if ($request->hasFile('photo')) {
            $oldPhoto = $admin->photo; // Simpan foto lama untuk log
            if ($admin->photo) {
                Storage::delete($admin->photo);  // Hapus foto lama jika ada
            }
            $path = $request->file('photo')->store('images_admin/photos');
            $admin->photo = $path;
            $changes['photo'] = ['old' => $oldPhoto, 'new' => $path];
        }
    
        $admin->save(); // Simpan perubahan
    
        // Catat log aktivitas
        $description = 'Profil admin diperbarui dengan perubahan: ' . json_encode($changes);
        LogActivity::create([
            'admin_id' => $admin->id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated profile with ID: ' . $admin->id,
        ]);
    
        return response()->json([
            'message' => 'Profil berhasil diupdate',
            'admin' => [
                'id' => $admin->id, // Tambahkan ID admin dalam respons
                'name' => $admin->name,
                'username' => $admin->username,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'photo' => $admin->photo_url,  // Menggunakan accessor untuk URL penuh foto
            ],
        ], 200);
    }
    
    
    public function changePassword(Request $request)
    {
        $request->validate([
            'newPassword' => 'required|string|min:8|confirmed',
        ]);
    
        $admin = Auth::user();
    
        if (!$admin) {
            return response()->json(['message' => 'Admin tidak ditemukan.'], 404);
        }
    
        $admin->password = Hash::make($request->newPassword);
        $admin->save();
    
        return response()->json(['message' => 'Password berhasil diperbarui.'], 200);
    }
    
    
}
