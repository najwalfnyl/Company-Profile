<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use App\Models\Admin;
use Illuminate\Http\Request;

class LogActivityController extends Controller
{
    // Menampilkan semua log aktivitas
    public function index()
    {
        $logActivities = LogActivity::with('admin')->get();
        return response()->json([
            'success' => true,
            'data' => $logActivities
        ], 200);
    }

    // Menyimpan log aktivitas baru
    public function store(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'date' => 'required|date',
            'time' => 'required',
            'action' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $logActivity = LogActivity::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Log Activity created successfully.',
            'data' => $logActivity
        ], 201);
    }

    // Menampilkan log aktivitas tertentu
    public function show($id)
    {
        $logActivity = LogActivity::with('admin')->find($id);

        if (!$logActivity) {
            return response()->json([
                'success' => false,
                'message' => 'Log Activity not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $logActivity
        ], 200);
    }

    // Memperbarui log aktivitas
    public function update(Request $request, $id)
    {
        $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'date' => 'required|date',
            'time' => 'required',
            'action' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $logActivity = LogActivity::find($id);

        if (!$logActivity) {
            return response()->json([
                'success' => false,
                'message' => 'Log Activity not found.'
            ], 404);
        }

        $logActivity->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Log Activity updated successfully.',
            'data' => $logActivity
        ], 200);
    }

    // Menghapus log aktivitas
    public function destroy($id)
    {
        $logActivity = LogActivity::find($id);

        if (!$logActivity) {
            return response()->json([
                'success' => false,
                'message' => 'Log Activity not found.'
            ], 404);
        }

        $logActivity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Log Activity deleted successfully.'
        ], 200);
    }
}
