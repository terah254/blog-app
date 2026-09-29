<x-mail::message>
# New Admin Comment

Hello {{ $notifiable->name }},

An admin has left a comment on your blog post **"{{ $blogTitle }}"**.

**Comment:**
> {{ $commentBody }}

<x-mail::button :url="$viewUrl">
View Your Post
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
