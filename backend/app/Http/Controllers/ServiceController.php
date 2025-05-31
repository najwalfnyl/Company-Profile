<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\LogActivity;
use Carbon\Carbon;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();

        $services->transform(function ($service) {
            if ($service->icon_service) {
                $service->icon_service = Storage::disk('public')->url($service->icon_service);
            }
            if ($service->icon_service2) {
                $service->icon_service2 = Storage::disk('public')->url($service->icon_service2);
            }
            if ($service->images_service) {
                $service->images_service = Storage::disk('public')->url($service->images_service);
            }
            return $service;
        });

        return response()->json($services);
    }


    public function show($id)
    {
        // Tampilkan data service berdasarkan id
        $service = Service::findOrFail($id);

        // Tambahkan URL lengkap untuk icon_service, icon_service2, dan images_service jika ada
        if ($service->icon_service) {
            $service->icon_service = Storage::disk('public')->url($service->icon_service);
        }
        if ($service->icon_service2) {
            $service->icon_service2 = Storage::disk('public')->url($service->icon_service2);
        }
        if ($service->images_service) {
            $service->images_service = Storage::disk('public')->url($service->images_service);
        }

        return response()->json($service);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_service' => 'nullable|file|mimes:jpg,png,jpeg,svg|max:2048',
            'icon_service2' => 'nullable|file|mimes:jpg,png,jpeg,svg|max:2048',
            'images_service' => 'nullable|file|mimes:jpg,png,jpeg|max:5120',
            'admin_id' => 'required|integer', // Admin ID untuk log
        ]);

        $iconPath = $request->hasFile('icon_service') ? $this->uploadFile($request->file('icon_service'), 'icons') : null;
        $iconPath2 = $request->hasFile('icon_service2') ? $this->uploadFile($request->file('icon_service2'), 'icons') : null;
        $imagesPath = $request->hasFile('images_service') ? $this->uploadFile($request->file('images_service'), 'images') : null;

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon_service' => $iconPath,
            'icon_service2' => $iconPath2,
            'images_service' => $imagesPath,
        ]);

        $service->icon_service = $iconPath ? Storage::disk('public')->url($iconPath) : null;
        $service->icon_service2 = $iconPath2 ? Storage::disk('public')->url($iconPath2) : null;
        $service->images_service = $imagesPath ? Storage::disk('public')->url($imagesPath) : null;

        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'), 
            'action' => 'Added',
            'description' => 'Added new service with ID:: ' . $service->id,
        ]);

        return response()->json($service, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_service' => 'nullable|file|mimes:jpg,png,jpeg,svg|max:2048',
            'icon_service2' => 'nullable|file|mimes:jpg,png,jpeg,svg|max:2048',
            'images_service' => 'nullable|file|mimes:jpg,png,jpeg|max:5120',
            'admin_id' => 'required|integer', // Admin ID untuk log
        ]);

        $service = Service::findOrFail($id);

        if ($request->hasFile('icon_service')) {
            if ($service->icon_service) {
                Storage::disk('public')->delete($service->icon_service);
            }
            $service->icon_service = $this->uploadFile($request->file('icon_service'), 'icons');
        }

        if ($request->hasFile('icon_service2')) {
            if ($service->icon_service2) {
                Storage::disk('public')->delete($service->icon_service2);
            }
            $service->icon_service2 = $this->uploadFile($request->file('icon_service2'), 'icons');
        }

        if ($request->hasFile('images_service')) {
            if ($service->images_service) {
                Storage::disk('public')->delete($service->images_service);
            }
            $service->images_service = $this->uploadFile($request->file('images_service'), 'images');
        }

        $service->name = $request->name;
        $service->description = $request->description;
        $service->save();

        $service->icon_service = $service->icon_service ? Storage::disk('public')->url($service->icon_service) : null;
        $service->icon_service2 = $service->icon_service2 ? Storage::disk('public')->url($service->icon_service2) : null;
        $service->images_service = $service->images_service ? Storage::disk('public')->url($service->images_service) : null;

        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated service with ID: ' . $id,
        ]);

        return response()->json($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            
            // Tambahkan pengecekan apakah file-file terkait memang ada
            if ($service->icon_service && Storage::disk('public')->exists($service->icon_service)) {
                Storage::disk('public')->delete($service->icon_service);
            }
            if ($service->icon_service2 && Storage::disk('public')->exists($service->icon_service2)) {
                Storage::disk('public')->delete($service->icon_service2);
            }
            if ($service->images_service && Storage::disk('public')->exists($service->images_service)) {
                Storage::disk('public')->delete($service->images_service);
            }
            
            $service->delete();
            
            LogActivity::create([
                'admin_id' => $request->admin_id,
                'date' => Carbon::now('Asia/Jakarta')->toDateString(),
                'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
                'action' => 'Deleted',
                'description' => 'Deleted service with ID: ' . $id,
            ]);
    
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Helper function to handle file upload with unique filenames.
     */
    private function uploadFile($file, $directory)
    {
        $filename = uniqid() . '_' . $file->getClientOriginalName();
        return $file->storeAs($directory, $filename, 'public');
    }
}
