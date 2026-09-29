<x-dashboard-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">My Blog Posts</h2>
                <a href="{{ route('blogs.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium">
                    + Write New Blog
                </a>
            </div>

            @forelse($blogs as $blog)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <a href="{{ route('blogs.show', $blog) }}" class="text-xl font-semibold text-gray-900 hover:text-indigo-600">
                                    {{ $blog->title }}
                                </a>
                                <p class="mt-2 text-gray-600">{{ Str::limit(strip_tags($blog->content), 150) }}</p>
                                <div class="mt-2 flex items-center gap-3 text-sm text-gray-500">
                                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $blog->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($blog->status) }}
                                    </span>
                                    <span>{{ $blog->created_at->diffForHumans() }}</span>
                                    <span>{{ $blog->comments_count }} admin comment(s)</span>
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('blogs.edit', $blog) }}" class="text-sm text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('blogs.destroy', $blog) }}" onsubmit="return confirm('Delete this blog?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-12 text-center text-gray-500">
                    You haven't written any blogs yet. Start writing!
                </div>
            @endforelse

            <div class="mt-4">{{ $blogs->links() }}</div>
        </div>
    </div>
</x-dashboard-layout>
