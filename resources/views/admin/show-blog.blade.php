<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:underline mb-4 inline-block">&larr; Back to Admin Dashboard</a>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $blog->title }}</h1>
                            <div class="mt-2 flex items-center gap-3 text-sm text-gray-500">
                                <span>By <strong>{{ $blog->user->name }}</strong></span>
                                <span>&bull;</span>
                                <span>{{ $blog->created_at->format('M d, Y H:i') }}</span>
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $blog->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($blog->status) }}
                                </span>
                            </div>
                            @if($blog->categories->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($blog->categories as $category)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.blog.delete', $blog) }}" onsubmit="return confirm('Delete this blog permanently?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm font-medium">
                                Delete Blog
                            </button>
                        </form>
                    </div>

                    @if($blog->image)
                        <div class="mt-6 rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="w-full h-auto object-cover">
                        </div>
                    @endif

                    <div class="mt-6 prose max-w-none text-gray-700 border-t pt-6">
                        {!! clean($blog->content) !!}</div>
                </div>
            </div>

            {{-- Comments --}}
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Your Admin Comments ({{ $blog->comments->count() }})</h3>

                    @forelse($blog->comments as $comment)
                        <div class="border-l-4 border-indigo-400 pl-4 mb-4">
                            <p class="text-sm text-gray-500">
                                <strong>{{ $comment->admin->name }}</strong> &bull; {{ $comment->created_at->diffForHumans() }}
                            </p>
                            <p class="text-gray-700 mt-1">{{ $comment->body }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No comments yet. Leave your feedback below.</p>
                    @endforelse

                    <form method="POST" action="{{ route('admin.blog.comment', $blog) }}" class="mt-6 border-t pt-4">
                        @csrf
                        <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Add Comment / Feedback</label>
                        <textarea name="body" id="body" rows="3" required placeholder="Write your review or feedback for the author..."
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <button type="submit" class="mt-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium">
                            Submit Comment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
