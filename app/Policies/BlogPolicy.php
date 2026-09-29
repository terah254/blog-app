<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;

class BlogPolicy
{
    /**
     * Determine whether the user can view the blog.
     */
    public function view(User $user, Blog $blog): bool
    {
        // Admins can view all blogs
        if ($user->isAdmin()) {
            return true;
        }

        // Users can view their own blogs
        if ($blog->user_id === $user->id) {
            return true;
        }

        // Others can only view published blogs
        return $blog->status === 'published';
    }

    /**
     * Determine whether the user can create blogs.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the blog.
     */
    public function update(User $user, Blog $blog): bool
    {
        return $blog->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the blog.
     */
    public function delete(User $user, Blog $blog): bool
    {
        return $blog->user_id === $user->id;
    }
}
