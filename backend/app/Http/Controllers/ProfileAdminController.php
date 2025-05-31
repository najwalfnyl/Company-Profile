<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileAdminController extends Controller
{
    /**
     * Tampilkan data profil yang ada
     */
    public function edit()
    {
        $admin = Auth::user(); // Mengambil data admin yang sedang login

        return response()->json([
            'admin' => [
                'name' => $admin->name,
                'username' => $admin->username,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'photo' => $admin->photo ? Storage::url($admin->photo) : null, // URL lengkap foto
            ]
        ]);
    }

    /**
     * Update data profil
     */
    public function update(Request $request)
    {
        $admin = Auth::user();

        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // maksimal 2MB
        ]);

        // Update nama dan nomor telepon
        $admin->name = $request->input('name');
        $admin->phone = $request->input('phone');

        // Update foto profil jika ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($admin->photo) {
                Storage::delete($admin->photo); // Menghapus foto dari storage
            }

            // Simpan foto baru
            $path = $request->file('photo')->store('public/photos');
            $admin->photo = $path; // Menyimpan path foto baru
        }

        // Simpan perubahan
        $admin->save();

        // Berikan respon balik setelah update berhasil
        return response()->json([
            'message' => 'Profil berhasil diupdate',
            'admin' => [
                'name' => $admin->name,
                'username' => $admin->username,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'photo' => $admin->photo ? Storage::url($admin->photo) : null, // URL lengkap foto baru
            ]
        ]);
    }
}
