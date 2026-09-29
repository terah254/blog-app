<?php

namespace App\Notifications;

use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAdminComment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment,
        public Blog $blog
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Admin Comment on Your Blog Post')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('An admin has left a comment on your blog post "' . $this->blog->title . '".')
            ->line('Comment: "' . \Illuminate\Support\Str::limit($this->comment->body, 100) . '"')
            ->action('View Your Post', route('blogs.show', $this->blog))
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'blog_id' => $this->blog->id,
            'blog_title' => $this->blog->title,
            'admin_name' => $this->comment->admin->name,
        ];
    }
}
