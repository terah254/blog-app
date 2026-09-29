<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Blog;
use App\Models\Comment;
use App\Notifications\NewAdminComment;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('user')
            ->withCount('comments')
            ->latest()
            ->paginate(15);

        $stats = [
            'total_blogs' => Blog::count(),
            'total_users' => \App\Models\User::where('role', 'user')->count(),
            'total_comments' => Comment::count(),
        ];

        return view('admin.dashboard', compact('blogs', 'stats'));
    }

    public function showBlog(Blog $blog)
    {
        $blog->load(['user', 'comments.admin', 'categories']);
        return view('admin.show-blog', compact('blog'));
    }

    public function deleteBlog(Blog $blog)
    {
        // Delete associated image
        if ($blog->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Blog deleted successfully.');
    }

    public function storeComment(StoreCommentRequest $request, Blog $blog)
    {
        $validated = $request->validated();

        $comment = Comment::create([
            'blog_id'  => $blog->id,
            'admin_id' => auth()->id(),
            'body'     => $validated['body'],
        ]);

        // Notify the blog author about the new comment
        $blog->user->notify(new NewAdminComment($comment, $blog));

        return redirect()->route('admin.blog.show', $blog)
                         ->with('success', 'Comment added.');
    }
}
