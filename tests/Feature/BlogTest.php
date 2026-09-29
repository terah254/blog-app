<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function user_can_create_blog(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('blogs.store'), [
                'title' => 'Test Blog Post',
                'content' => 'This is test content.',
                'status' => 'published',
            ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertDatabaseHas('blogs', [
            'title' => 'Test Blog Post',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_can_update_own_blog(): void
    {
        $blog = Blog::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->put(route('blogs.update', $blog), [
                'title' => 'Updated Title',
                'content' => 'Updated content.',
                'status' => 'published',
            ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function user_cannot_update_others_blog(): void
    {
        $blog = Blog::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->user)
            ->put(route('blogs.update', $blog), [
                'title' => 'Updated Title',
                'content' => 'Updated content.',
                'status' => 'published',
            ]);

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_delete_own_blog(): void
    {
        $blog = Blog::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('blogs.destroy', $blog));

        $response->assertRedirect(route('user.dashboard'));
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    /** @test */
    public function user_cannot_delete_others_blog(): void
    {
        $blog = Blog::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('blogs.destroy', $blog));

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_view_all_blogs(): void
    {
        Blog::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertOk();
    }

    /** @test */
    public function user_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_comment_on_blog(): void
    {
        $blog = Blog::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.blog.comment', $blog), [
                'body' => 'Great post!',
            ]);

        $response->assertRedirect(route('admin.blog.show', $blog));
        $this->assertDatabaseHas('comments', [
            'blog_id' => $blog->id,
            'admin_id' => $this->admin->id,
            'body' => 'Great post!',
        ]);
    }

    /** @test */
    public function user_cannot_comment_on_blog(): void
    {
        $blog = Blog::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('admin.blog.comment', $blog), [
                'body' => 'Comment from user',
            ]);

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_delete_any_blog(): void
    {
        $blog = Blog::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.blog.delete', $blog));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    /** @test */
    public function published_blogs_are_visible_to_guests(): void
    {
        Blog::factory()->create(['status' => 'published']);
        Blog::factory()->create(['status' => 'draft']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('blogs', fn ($blogs) => $blogs->count() === 1);
    }

    /** @test */
    public function draft_blogs_are_not_visible_to_guests(): void
    {
        $blog = Blog::factory()->create(['status' => 'draft']);

        $response = $this->get(route('blogs.show', $blog));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_view_own_draft_blog(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('blogs.show', $blog));

        $response->assertOk();
    }

    /** @test */
    public function user_cannot_view_others_draft_blog(): void
    {
        $blog = Blog::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('blogs.show', $blog));

        $response->assertForbidden();
    }

    /** @test */
    public function blog_creation_requires_valid_data(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('blogs.store'), [
                'title' => '',
                'content' => '',
                'status' => 'invalid',
            ]);

        $response->assertSessionHasErrors(['title', 'content', 'status']);
    }

    /** @test */
    public function blog_image_upload_works(): void
    {
        $image = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($this->user)
            ->post(route('blogs.store'), [
                'title' => 'Blog with Image',
                'content' => 'Content here',
                'status' => 'published',
                'image' => $image,
            ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertDatabaseHas('blogs', ['title' => 'Blog with Image']);
    }
}
