<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    // Endpoint untuk mengambil password lama
    public function getOldPassword(Request $request)
    {
        // Validasi email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Temukan admin berdasarkan email
        $admin = Admin::where('email', $request->email)->first();

        // Jika admin tidak ditemukan, berikan pesan error
        if (!$admin) {
            return response()->json(['message' => 'Email tidak ditemukan.'], 404);
        }

        // Kembalikan hash password yang lama (Tidak mengembalikan password asli)
        return response()->json(['oldPasswordHash' => $admin->password], 200);
    }

    // Reset password
    public function reset(Request $request)
    {
        // Validasi input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed', // Validasi panjang password minimal 8 karakter
        ]);

        // Temukan admin berdasarkan email
        $admin = Admin::where('email', $request->email)->first();

        // Jika admin tidak ditemukan, berikan pesan error
        if (!$admin) {
            return response()->json(['message' => 'Email tidak ditemukan.'], 404);
        }

        // Periksa apakah token valid dan belum kadaluarsa
        if (!$admin->isPasswordResetTokenValid($request->token)) {
            return response()->json(['message' => 'Token reset password sudah kadaluarsa.'], 400);
        }

        // Periksa apakah password baru sama dengan password lama
        if (Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Password baru tidak boleh sama dengan password lama.'], 400);
        }

        // Reset password dan hapus token setelah digunakan
        $admin->password = Hash::make($request->password);
        $admin->clearPasswordResetToken(); // Hapus token setelah digunakan
        $admin->save();

        return response()->json(['message' => 'Password berhasil direset.'], 200);
    }

    public function checkResetToken(Request $request)
    {
        // Validasi input
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
        ]);
    
        // Temukan admin berdasarkan email
        $admin = Admin::where('email', $request->email)->first();
    
        // Jika admin tidak ditemukan, berikan pesan error
        if (!$admin) {
            return response()->json(['message' => 'Email tidak ditemukan.'], 404);
        }
    
        // Periksa apakah token valid dan belum kadaluarsa
        if (!$admin->isPasswordResetTokenValid($request->token)) {
            return response()->json(['message' => 'Token expired atau tidak valid.'], 400);
        }
    
        // Jika token valid
        return response()->json(['message' => 'Token valid'], 200);
    }
    

}
