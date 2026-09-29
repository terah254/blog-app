<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ApiBlogController extends Controller
{
    /**
     * Display a listing of blogs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Blog::with('user')
            ->withCount('comments')
            ->with('categories');

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $blogs = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => BlogResource::collection($blogs),
            'meta' => [
                'current_page' => $blogs->currentPage(),
                'last_page' => $blogs->lastPage(),
                'per_page' => $blogs->perPage(),
                'total' => $blogs->total(),
            ],
        ]);
    }

    /**
     * Store a newly created blog.
     */
    public function store(StoreBlogRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blog-images', 'public');
        }

        $blog = $request->user()->blogs()->create($validated);

        if ($request->has('categories')) {
            $blog->categories()->attach($request->input('categories'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully',
            'data' => new BlogResource($blog->load(['user', 'categories'])),
        ], 201);
    }

    /**
     * Display the specified blog.
     */
    public function show(Blog $blog): JsonResponse
    {
        Gate::authorize('view', $blog);

        return response()->json([
            'success' => true,
            'data' => new BlogResource($blog->load(['user', 'comments.admin', 'categories'])),
        ]);
    }

    /**
     * Update the specified blog.
     */
    public function update(UpdateBlogRequest $request, Blog $blog): JsonResponse
    {
        Gate::authorize('update', $blog);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $validated['image'] = $request->file('image')->store('blog-images', 'public');
        }

        $blog->update($validated);

        if ($request->has('categories')) {
            $blog->categories()->sync($request->input('categories'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully',
            'data' => new BlogResource($blog->load(['user', 'categories'])),
        ]);
    }

    /**
     * Remove the specified blog.
     */
    public function destroy(Blog $blog): JsonResponse
    {
        Gate::authorize('delete', $blog);

        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully',
        ]);
    }
}
