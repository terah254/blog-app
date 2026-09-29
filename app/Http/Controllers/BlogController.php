<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class BlogController extends Controller
{
    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        $categories = Category::all();
        return view('blogs.create', compact('categories'));
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(StoreBlogRequest $request)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blog-images', 'public');
        }

        $blog = auth()->user()->blogs()->create($validated);

        // Attach categories
        if ($request->has('categories')) {
            $blog->categories()->attach($request->input('categories'));
        }

        return redirect()->route('user.dashboard')->with('success', 'Blog post created successfully!');
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit(Blog $blog)
    {
        Gate::authorize('update', $blog);

        $categories = Category::all();
        return view('blogs.edit', compact('blog', 'categories'));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        Gate::authorize('update', $blog);

        $validated = $request->validated();

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete the old image from storage if it exists
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            // Store the new image
            $validated['image'] = $request->file('image')->store('blog-images', 'public');
        }

        $blog->update($validated);

        // Sync categories
        $blog->categories()->sync($request->input('categories', []));

        return redirect()->route('user.dashboard')->with('success', 'Blog post updated successfully!');
    }

    /**
     * Remove the specified blog from storage.
     */
    public function destroy(Blog $blog)
    {
        Gate::authorize('delete', $blog);

        // Delete the image file from storage
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return redirect()->route('user.dashboard')->with('success', 'Blog post deleted successfully.');
    }

    /**
     * Display the specified blog.
     */
    public function show(Blog $blog)
    {
        Gate::authorize('view', $blog);

        $blog->load(['comments.admin', 'user', 'categories']);

        return view('blogs.show', compact('blog'));
    }
}
