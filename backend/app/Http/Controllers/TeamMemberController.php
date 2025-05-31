<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\LogActivity;  // Import LogActivity
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teamMembers = TeamMember::all();

        // Convert photo path to full URL if photo exists
        $teamMembers->transform(function ($teamMember) {
            if ($teamMember->photo) {
                $teamMember->photo = Storage::disk('public')->url($teamMember->photo);
            }
            return $teamMember;
        });

        return response()->json($teamMembers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'admin_id' => 'required|integer', // Admin ID for logging activity
        ]);

        // Process file upload with unique filenames
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // Generate a unique filename
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            // Store the file with the unique name
            $photoPath = $file->storeAs('team_members', $filename, 'public');
        }

        // Save to the database
        $teamMember = TeamMember::create([
            'name' => $validatedData['name'],
            'position' => $validatedData['position'],
            'photo' => $photoPath,
        ]);

        // Convert photo path to full URL if photo exists
        $teamMember->photo = $photoPath ? Storage::disk('public')->url($photoPath) : null;

        // Log activity
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new team member with ID: ' . $teamMember->id,
        ]);

        return response()->json($teamMember, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $teamMember = TeamMember::findOrFail($id);

        // Convert photo path to full URL if photo exists
        $teamMember->photo = $teamMember->photo ? Storage::disk('public')->url($teamMember->photo) : null;

        return response()->json($teamMember);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $teamMember = TeamMember::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'admin_id' => 'required|integer', // Admin ID for logging activity
        ]);

        // Process file upload with unique filenames if photo is present
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
            }

            // Store the new photo with a unique filename
            $file = $request->file('photo');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $teamMember->photo = $file->storeAs('team_members', $filename, 'public');
        }

        // Update other fields
        $teamMember->update($request->except(['photo']));

        // Convert photo path to full URL if photo exists
        $teamMember->photo = $teamMember->photo ? Storage::disk('public')->url($teamMember->photo) : null;

        // Log activity
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated team member with ID: ' . $id,
        ]);

        return response()->json($teamMember, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $teamMember = TeamMember::findOrFail($id);

        // Delete photo if exists
        if ($teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
        }

        $teamMember->delete();

        // Log activity
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Deleted',
            'description' => 'Deleted team member with ID: ' . $id,
        ]);

        return response()->json($teamMember, 200);
    }
}
