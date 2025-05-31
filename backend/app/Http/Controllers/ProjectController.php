<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\LogActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');
        
        $query = Project::with(['category', 'perusahaan', 'superiorities']);
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
    
        $projects = $query->latest()->get();
    
        $projects->transform(function ($project) {
            if ($project->picture) {
                $project->picture = Storage::disk('public')->url($project->picture);
            }
            foreach (['picture01', 'picture02', 'picture03', 'picture04'] as $pictureField) {
                if ($project->$pictureField) {
                    $project->$pictureField = Storage::disk('public')->url($project->$pictureField);
                }
            }
            return $project;
        });
    
        return response()->json($projects);
    }

    public function show($id)
    {
        // Memuat project beserta relasi kategori, perusahaan, dan superiorities
        $project = Project::with(['category', 'perusahaan', 'superiorities'])->find($id);
        
        if ($project) {
            // Mengubah path gambar utama project
            $project->picture = $project->picture ? Storage::disk('public')->url($project->picture) : null;
            
            // Mengubah path gambar lain (picture01, picture02, picture03, picture04)
            foreach (['picture01', 'picture02', 'picture03', 'picture04'] as $pictureField) {
                if ($project->$pictureField) {
                    $project->$pictureField = Storage::disk('public')->url($project->$pictureField);
                }
            }
            
            // Mengubah path logo_superiority untuk setiap item di superiorities
            if ($project->superiorities) {
                foreach ($project->superiorities as $superiority) {
                    if ($superiority->logo_superiority) {
                        // Ubah logo_superiority menjadi URL yang bisa diakses
                        $superiority->logo_superiority = Storage::disk('public')->url($superiority->logo_superiority);
                    }
                }
            }
    
            return response()->json($project);
        }
    
        return response()->json(['message' => 'Project not found'], 404);
    }
    
    

    public function store(Request $request)
    {
        // Validasi request
        $request->validate([
            'name_project' => 'required|string|max:255',
            'sub_title' => 'required|string|max:255',
            'category_id' => 'required|exists:project_categories,id_category',
            'perusahaan_id' => 'required|exists:perusahaans,id',
            'picture' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'description1' => 'required|string',
            'tanggal' => 'required|date',
            'picture01' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'picture02' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'picture03' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'picture04' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'description2' => 'nullable|string',
            'description3' => 'nullable|string',
            'superiority_id' => 'array', // Validasi bahwa superiority_id adalah array
            'superiority_id.*' => 'exists:superiorities,id', // Pastikan setiap ID ada di tabel superiorities
        ]);

        // Proses file upload
        $picturePath = $request->file('picture') ? $request->file('picture')->store('projects', 'public') : null;
        $picture01Path = $request->file('picture01') ? $request->file('picture01')->store('projects', 'public') : null;
        $picture02Path = $request->file('picture02') ? $request->file('picture02')->store('projects', 'public') : null;
        $picture03Path = $request->file('picture03') ? $request->file('picture03')->store('projects', 'public') : null;
        $picture04Path = $request->file('picture04') ? $request->file('picture04')->store('projects', 'public') : null;

        // Simpan data ke dalam database
        $project = Project::create([
            'name_project' => $request->name_project,
            'sub_title'=> $request->sub_title,
            'category_id' => $request->category_id,
            'perusahaan_id' => $request->perusahaan_id,
            'picture' => $picturePath,
            'description1' => $request->description1,
            'tanggal' => $request->tanggal,
            'picture01' => $picture01Path,
            'picture02' => $picture02Path,
            'picture03' => $picture03Path,
            'picture04' => $picture04Path,
            'description2' => $request->description2,
            'description3' => $request->description3,
        ]);

        // Attach superiorities ke project
        if ($request->has('superiority_id')) {
            $project->superiorities()->attach($request->superiority_id);
        }

        // Ubah path menjadi URL lengkap
        $project->picture = $picturePath ? Storage::disk('public')->url($picturePath) : null;
        foreach (['picture01', 'picture02', 'picture03', 'picture04'] as $pictureField) {
            if ($project->$pictureField) {
                $project->$pictureField = Storage::disk('public')->url($project->$pictureField);
            }
        }
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new project with ID:: ' . $project->id_project,
        ]);

        return response()->json($project, 201);
    }

    public function update(Request $request, $id)
    {
        // Temukan project berdasarkan ID
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }
    
        // Validasi request
        $request->validate([
            'name_project' => 'required|string|max:255',
            'sub_title' => 'required|string|max:255',
            'category_id' => 'required|exists:project_categories,id_category',
            'perusahaan_id' => 'required|exists:perusahaans,id',
            'picture' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'description1' => 'required|string',
            'tanggal' => 'required|date',
            'picture01' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'picture02' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'picture03' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'picture04' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'description2' => 'nullable|string',
            'description3' => 'nullable|string',
            'superiority_id' => 'array', // Validasi bahwa superiority_id adalah array
            'superiority_id.*' => 'exists:superiorities,id', // Pastikan setiap ID ada di tabel superiorities
        ]);
    
        // Mengelola file gambar utama
        if ($request->hasFile('picture')) {
            if ($project->picture) {
                Storage::disk('public')->delete($project->picture);
            }
            $project->picture = $request->file('picture')->store('projects', 'public');
        }
    
        // Mengelola file gambar tambahan (picture01 - picture04)
        foreach (['picture01', 'picture02', 'picture03', 'picture04'] as $pictureField) {
            if ($request->hasFile($pictureField)) {
                if ($project->$pictureField) {
                    Storage::disk('public')->delete($project->$pictureField);
                }
                $project->$pictureField = $request->file($pictureField)->store('projects', 'public');
            }
        }
    
        // Update data lain yang tidak termasuk file
        $project->update([
            'name_project' => $request->name_project,
            'sub_title' => $request->sub_title,
            'category_id' => $request->category_id,
            'perusahaan_id' => $request->perusahaan_id,
            'description1' => $request->description1,
            'tanggal' => $request->tanggal,
            'description2' => $request->description2,
            'description3' => $request->description3,
        ]);
    
        // Sync (hapus dan tambahkan) superiorities ke project
        if ($request->has('superiority_id')) {
            $project->superiorities()->sync($request->superiority_id); // Menghapus relasi lama dan menambahkan yang baru
        }
    
        // Ubah path file gambar menjadi URL lengkap
        $project->picture = $project->picture ? Storage::disk('public')->url($project->picture) : null;
        foreach (['picture01', 'picture02', 'picture03', 'picture04'] as $pictureField) {
            if ($project->$pictureField) {
                $project->$pictureField = Storage::disk('public')->url($project->$pictureField);
            }
        }
    

        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated project with ID: ' . $id,
        ]);

        return response()->json($project, 200);
    }

    public function destroy(Request $request, $id)
{
    $project = Project::find($id);
    if ($project) {
        // Hapus semua file gambar jika ada
        foreach (['picture', 'picture01', 'picture02', 'picture03', 'picture04'] as $pictureField) {
            if ($project->$pictureField) {
                Storage::disk('public')->delete($project->$pictureField);
            }
        }

        // Detach semua superiorities yang terkait dengan project
        $project->superiorities()->detach();

        // Hapus project dari database
        $project->delete();

        // Log aktivitas untuk penghapusan project
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Deleted',
            'description' => 'Deleted project with ID: ' . $id,
        ]);

        return response()->json(['message' => 'Project deleted successfully'], 200);
    }

    return response()->json(['message' => 'Project not found'], 404);
}
}