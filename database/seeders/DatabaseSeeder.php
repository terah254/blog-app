<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user (or update if exists)
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // Create regular user (or update if exists)
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'role' => 'user',
                'password' => Hash::make('password'),
            ]
        );

        // Create categories
        $categories = [
            'Technology',
            'Lifestyle',
            'Business',
            'Travel',
            'Food',
            'Health',
            'Entertainment',
            'Sports',
        ];

        foreach ($categories as $categoryName) {
            Category::updateOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );
        }

        // Create sample blogs
        $allCategories = Category::all();

        for ($i = 0; $i < 10; $i++) {
            $blog = Blog::factory()->create([
                'user_id' => $user->id,
                'status' => $i < 7 ? 'published' : 'draft',
            ]);

            // Attach random categories
            $randomCategories = $allCategories->random(rand(1, 3));
            $blog->categories()->attach($randomCategories->pluck('id'));

            // Add some comments to published blogs
            if ($blog->status === 'published' && $i < 5) {
                Comment::factory()->count(rand(1, 3))->create([
                    'blog_id' => $blog->id,
                    'admin_id' => $admin->id,
                ]);
            }
        }
    }
}
