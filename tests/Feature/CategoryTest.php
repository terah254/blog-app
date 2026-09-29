<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function user_can_create_blog_with_categories(): void
    {
        $categories = Category::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->post(route('blogs.store'), [
                'title' => 'Blog with Categories',
                'content' => 'Content here',
                'status' => 'published',
                'categories' => [$categories[0]->id, $categories[1]->id],
            ]);

        $response->assertRedirect(route('user.dashboard'));
        
        $blog = Blog::where('title', 'Blog with Categories')->first();
        $this->assertNotNull($blog);
        $this->assertCount(2, $blog->categories);
    }

    /** @test */
    public function user_can_update_blog_categories(): void
    {
        $blog = Blog::factory()->create(['user_id' => $this->user->id]);
        $categories = Category::factory()->count(2)->create();

        $response = $this->actingAs($this->user)
            ->put(route('blogs.update', $blog), [
                'title' => $blog->title,
                'content' => $blog->content,
                'status' => $blog->status,
                'categories' => [$categories[0]->id],
            ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertCount(1, $blog->fresh()->categories);
    }

    /** @test */
    public function categories_are_displayed_on_home_page(): void
    {
        $category = Category::factory()->create(['name' => 'Technology']);
        $blog = Blog::factory()->create(['status' => 'published']);
        $blog->categories()->attach($category);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Technology');
    }

    /** @test */
    public function blogs_can_be_filtered_by_category(): void
    {
        $category = Category::factory()->create(['name' => 'Technology', 'slug' => 'technology']);
        $blog1 = Blog::factory()->create(['status' => 'published']);
        $blog1->categories()->attach($category);
        
        $blog2 = Blog::factory()->create(['status' => 'published']);

        $response = $this->get(route('home', ['category' => 'technology']));

        $response->assertOk();
        $response->assertSee($blog1->title);
        $response->assertDontSee($blog2->title);
    }

    /** @test */
    public function blogs_can_be_searched(): void
    {
        Blog::factory()->create(['title' => 'Laravel Tutorial', 'status' => 'published']);
        Blog::factory()->create(['title' => 'Vue.js Guide', 'status' => 'published']);

        $response = $this->get(route('home', ['search' => 'Laravel']));

        $response->assertOk();
        $response->assertSee('Laravel Tutorial');
        $response->assertDontSee('Vue.js Guide');
    }
}
