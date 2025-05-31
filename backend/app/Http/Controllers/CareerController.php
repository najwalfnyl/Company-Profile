<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\LogActivity; // Import model LogActivity
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // Untuk pencatatan waktu

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $careers = Career::all();

        // Add full URL for images_career if it exists
        $careers->transform(function ($career) {
            if ($career->images_career) {
                $career->images_career = Storage::disk('public')->url($career->images_career);
            }
            return $career;
        });

        return response()->json($careers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'images_career' => 'nullable|file|mimes:jpg,png,jpeg|max:2048', // File validation
            'admin_id' => 'required|integer', // Tambahkan validasi admin_id
        ]);

        // Handle file upload if it exists
        $imagePath = null;
        if ($request->hasFile('images_career')) {
            $file = $request->file('images_career');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $imagePath = $file->storeAs('careers', $filename, 'public');
        }

        // Create a new career entry
        $career = Career::create([
            'name' => $request->name,
            'description' => $request->description,
            'images_career' => $imagePath,
        ]);

        // Log activity
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new career with ID:: ' . $career->id,
        ]);

        $career->images_career = $imagePath ? Storage::disk('public')->url($imagePath) : null;

        return response()->json($career, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $career = Career::findOrFail($id);

        if ($career->images_career) {
            $career->images_career = Storage::disk('public')->url($career->images_career);
        }

        return response()->json($career);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'images_career' => 'nullable|file|mimes:jpg,png,jpeg|max:2048', // File validation
            'admin_id' => 'required|integer', // Tambahkan validasi admin_id
        ]);

        $career = Career::findOrFail($id);

        if ($request->hasFile('images_career')) {
            if ($career->images_career) {
                Storage::disk('public')->delete($career->images_career);
            }

            $file = $request->file('images_career');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $career->images_career = $file->storeAs('careers', $filename, 'public');
        }

        $career->name = $request->name;
        $career->description = $request->description;
        $career->save();

        // Log activity
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated career with ID:: ' . $career->id,
        ]);

        $career->images_career = $career->images_career ? Storage::disk('public')->url($career->images_career) : null;

        return response()->json($career);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'admin_id' => 'required|integer', // Tambahkan validasi admin_id
        ]);

        $career = Career::findOrFail($id);

        if ($career->images_career) {
            Storage::disk('public')->delete($career->images_career);
        }

        $career->delete();

        // Log activity
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Deleted',
            'description' => 'Deleted career with ID:: ' . $id,
        ]);

        return response()->json(null, 204);
    }
}
