<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        $blogs = auth()->user()->blogs()
            ->withCount('comments')
            ->with('categories')
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => $blogs->total(),
            'published' => auth()->user()->blogs()->where('status', 'published')->count(),
            'drafts' => auth()->user()->blogs()->where('status', 'draft')->count(),
            'comments' => auth()->user()->blogs()->withCount('comments')->get()->sum('comments_count'),
        ];

        return view('user.dashboard', compact('blogs', 'stats'));
    }
}
