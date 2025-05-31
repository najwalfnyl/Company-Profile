<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\KeywordBlog;
use App\Models\LogActivity;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data blog beserta relasi keyword_blog dan tags
        $blogs = Blog::with(['keywordBlog', 'tags'])->get();

        // Menambahkan URL lengkap untuk images_blog jika ada
        $blogs->transform(function ($blog) {
            if ($blog->images_blog) {
                $blog->images_blog = Storage::disk('public')->url($blog->images_blog);
            }
            return $blog;
        });

        return response()->json($blogs);
    }

    public function show($id)
{
    try {
        $blog = Blog::with(['keywordBlog', 'tags'])->findOrFail($id);

        if ($blog->images_blog) {
            $blog->images_blog = Storage::disk('public')->url($blog->images_blog);
        }

        return response()->json($blog);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Blog not found'], 404);
    }
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'keyword_blog_id' => 'required|exists:keyword_blogs,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'images_blog' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'date' => 'required|date',
            'admin_id' => 'required|exists:admins,id',
            'tag_id' => 'array|max:5', // Maksimal 5 tag
            'tag_id.*' => 'integer|exists:tags,id', // Validasi ID tag
        ]);
    
        // Proses file upload jika ada
        $imagePath = null;
        if ($request->hasFile('images_blog')) {
            $file = $request->file('images_blog');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $imagePath = $file->storeAs('blogs', $filename, 'public');
        }
    
        // Buat data blog baru
        $blog = Blog::create([
            'keyword_blog_id' => $request->keyword_blog_id,
            'title' => $request->title,
            'description' => $request->description,
            'images_blog' => $imagePath,
            'date' => $request->date,
        ]);
    
        // Sinkronisasi tag_id
        if ($request->tag_id) {
            $blog->tags()->sync($request->tag_id);
        }
    
        // Catat log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Added',
            'description' => 'Added new blog with ID: ' . $blog->id,
        ]);
    
        $blog->images_blog = $imagePath ? Storage::disk('public')->url($imagePath) : null;
    
        return response()->json($blog->load('tags'), 201);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang dikirimkan
        $request->validate([
            'keyword_blog_id' => 'required|exists:keyword_blogs,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'images_blog' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            'date' => 'required|date',
            'admin_id' => 'required|exists:admins,id',
            'tag_id' => 'array|max:5', // Maksimal 5 tag
            'tag_id.*' => 'integer|exists:tags,id', // Validasi ID tag
        ]);
    
        $blog = Blog::findOrFail($id);
    
        // Proses file upload jika ada
        if ($request->hasFile('images_blog')) {
            if ($blog->images_blog) {
                Storage::disk('public')->delete($blog->images_blog);
            }
    
            $file = $request->file('images_blog');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $imagePath = $file->storeAs('blogs', $filename, 'public');
            $blog->images_blog = $imagePath;
        }
    
        $blog->update([
            'keyword_blog_id' => $request->keyword_blog_id,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
        ]);
    
        // Sinkronisasi tag_id
        if ($request->tag_id) {
            $blog->tags()->sync($request->tag_id);
        }
    
        // Catat log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Updated',
            'description' => 'Updated blog with ID: ' . $blog->id,
        ]);
    
        $blog->images_blog = $blog->images_blog ? Storage::disk('public')->url($blog->images_blog) : null;
    
        return response()->json([
            'message' => 'Blog updated successfully',
            'data' => $blog->load('tags'),
        ], 200);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->tags()->detach();

        if ($blog->images_blog) {
            Storage::disk('public')->delete($blog->images_blog);
        }

        $blog->delete();

        // Catat log aktivitas
        LogActivity::create([
            'admin_id' => $request->admin_id,
            'date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
            'action' => 'Deleted',
            'description' => 'Deleted blog with ID: ' . $id,
        ]);

        return response()->json([
            'message' => 'Blog deleted successfully',
        ], 200);
    }
}
