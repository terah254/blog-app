<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserDashboardController;
use App\Models\Blog;
use Illuminate\Support\Facades\Route;

// Public home
Route::get('/', function (\Illuminate\Http\Request $request) {
    $query = Blog::where('status', 'published')
                 ->with('user')
                 ->withCount('comments');

    // Search functionality
    if ($request->filled('search')) {
        $query->search($request->input('search'));
    }

    // Category filter
    if ($request->filled('category')) {
        $query->whereHas('categories', function ($q) use ($request) {
            $q->where('slug', $request->input('category'));
        });
    }

    $blogs = $query->latest()->paginate(9);

    $categories = \App\Models\Category::withCount('blogs')->get();

    return view('home', compact('blogs', 'categories'));
})->name('home');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Smart redirect based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User routes
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/blogs/create', [BlogController::class, 'create'])->name('blogs.create');
    Route::post('/blogs', [BlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{blog}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{blog}', [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy');
    Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blogs.show');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/blogs/{blog}', [AdminDashboardController::class, 'showBlog'])->name('blog.show');
        Route::delete('/blogs/{blog}', [AdminDashboardController::class, 'deleteBlog'])->name('blog.delete');
        Route::post('/blogs/{blog}/comments', [AdminDashboardController::class, 'storeComment'])
            ->name('blog.comment')
            ->middleware('throttle:10,1'); // Rate limit: 10 comments per minute
    });
});

require __DIR__.'/auth.php';
