<?php
namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galleries = Gallery::all();

        // Convert file paths to URLs
        $galleries->transform(function ($gallery) {
            for ($i = 1; $i <= 7; $i++) {
                $imageField = "image_activity" . $i;
                if ($gallery->$imageField) {
                    $gallery->$imageField = Storage::disk('public')->url($gallery->$imageField);
                }
            }
            return $gallery;
        });

        return response()->json($galleries);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'image_activity1' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity2' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity3' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity4' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity5' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity6' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity7' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
        ]);

        $galleryData = [];
        for ($i = 1; $i <= 7; $i++) {
            $file = $request->file("image_activity$i");
            if ($file) {
                $filename = uniqid() . '_' . $file->getClientOriginalName();
                $galleryData["image_activity$i"] = $file->storeAs('gallery', $filename, 'public');
            }
        }

        $gallery = Gallery::create($galleryData);

        // Log Activity
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new gallery with ID:: ' . $gallery->id,
        ]);

        return response()->json($gallery, 201);
    }

    public function show($id)
    {
        $gallery = Gallery::findOrFail($id);

        for ($i = 1; $i <= 7; $i++) {
            $imageField = "image_activity$i";
            if ($gallery->$imageField) {
                $gallery->$imageField = Storage::disk('public')->url($gallery->$imageField);
            }
        }

        return response()->json($gallery);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        $validatedData = $request->validate([
            'image_activity1' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity2' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity3' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity4' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity5' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity6' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'image_activity7' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
        ]);

        for ($i = 1; $i <= 7; $i++) {
            if ($request->hasFile("image_activity$i")) {
                if ($gallery->{"image_activity$i"}) {
                    Storage::disk('public')->delete($gallery->{"image_activity$i"});
                }
                $file = $request->file("image_activity$i");
                $filename = uniqid() . '_' . $file->getClientOriginalName();
                $gallery->{"image_activity$i"} = $file->storeAs('gallery', $filename, 'public');
            }
        }

        $gallery->save();

        // Log Activity
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated gallery with ID:: ' . $gallery->id,
        ]);

        return response()->json($gallery);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);

        for ($i = 1; $i <= 7; $i++) {
            $imageField = "image_activity$i";
            if ($gallery->$imageField) {
                Storage::disk('public')->delete($gallery->$imageField);
            }
        }

        $gallery->delete();

        // Log Activity
        LogActivity::create([
            'admin_id' => request()->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Deleted',
            'description' => 'Deleted gallery with ID:: ' . $id,
        ]);

        return response()->json(null, 204);
    }
}
