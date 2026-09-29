<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Main Blog Post Card --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-8 md:p-12">
                    {{-- Back Button --}}
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" 
                       class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-8 font-medium transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Dashboard
                    </a>

                    {{-- Title & Meta --}}
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight">{{ $blog->title }}</h1>
                     
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 border-b border-gray-100 pb-6 mb-8">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                {{ substr($blog->user->name, 0, 1) }}
                            </div>
                            <span class="font-medium text-gray-700">By {{ $blog->user->name }}</span>
                        </div>
                        <span class="text-gray-300">&bull;</span>
                        <span>{{ $blog->created_at->format('F d, Y') }}</span>
                        <span class="text-gray-300">&bull;</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $blog->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($blog->status) }}
                        </span>
                    </div>

                    {{-- Featured Image --}}
                    @if($blog->image)
                        <div class="mb-10 rounded-xl overflow-hidden shadow-lg">
                            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="w-full h-auto object-cover">
                        </div>
                    @endif

                    {{-- Blog Content (Sanitized) --}}
                    <div class="blog-content text-gray-700 text-lg leading-relaxed space-y-4">
                        {!! clean($blog->content) !!}
                    </div>
                </div>
            </div>

            {{-- Admin Comments Section --}}
            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-8 md:p-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Admin Feedback & Comments ({{ $blog->comments->count() }})
                    </h3>

                    @forelse($blog->comments as $comment)
                        <div class="border-l-4 border-indigo-400 bg-gray-50 pl-6 py-4 pr-4 mb-4 rounded-r-lg shadow-sm">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                    {{ substr($comment->admin->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $comment->admin->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <p class="text-gray-700 mt-2">{{ $comment->body }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                            <p class="text-gray-500">No admin comments or feedback yet.</p>
                        </div>
                    @endforelse

                    {{-- Admin Comment Form --}}
                    @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('admin.blog.comment', $blog) }}" class="mt-8 border-t border-gray-100 pt-8">
                            @csrf
                            <label for="body" class="block text-sm font-bold text-gray-700 mb-2">Leave Feedback for the Author</label>
                            <textarea name="body" id="body" rows="4" required placeholder="Write your review, suggestions, or feedback..."
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"></textarea>
                            @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            <button type="submit" class="mt-4 bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 font-medium transition shadow-md flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Post Comment
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Custom Styles for Blog Content --}}
    <style>
        .blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4 {
            font-weight: 700;
            color: #1f2937;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        .blog-content h1 { font-size: 2rem; }
        .blog-content h2 { font-size: 1.75rem; }
        .blog-content h3 { font-size: 1.5rem; }
         
        .blog-content p {
            margin-bottom: 1.25rem;
        }
         
        .blog-content ul, .blog-content ol {
            margin-bottom: 1.25rem;
            padding-left: 1.5rem;
        }
        .blog-content ul { list-style-type: disc; }
        .blog-content ol { list-style-type: decimal; }
        .blog-content li {
            margin-bottom: 0.5rem;
        }
         
        .blog-content blockquote {
            border-left: 4px solid #6366f1;
            padding-left: 1rem;
            font-style: italic;
            color: #4b5563;
            background-color: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1.5rem 0;
        }
         
        .blog-content img {
            border-radius: 0.75rem;
            margin: 1.5rem 0;
            max-width: 100%;
            height: auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
         
        .blog-content a {
            color: #4f46e5;
            text-decoration: underline;
            font-weight: 500;
        }
        .blog-content a:hover {
            color: #4338ca;
        }
    </style>
</x-app-layout>
