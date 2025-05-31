<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;


class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        // Validasi email
        $request->validate(['email' => 'required|email']);
    
        // Cari Admin berdasarkan email
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return response()->json(['message' => 'Admin tidak ditemukan.'], 404);
        }
    
        // Generate reset token dan simpan di database
        $admin->generatePasswordResetToken();
    
        return response()->json(['message' => 'Link reset password telah dikirim.'], 200);
    }
    
}
