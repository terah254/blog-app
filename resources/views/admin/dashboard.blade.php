<x-dashboard-layout>
    {{-- Admin Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-indigo-600">
            <p class="text-gray-500 text-sm font-medium">Total Blogs</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_blogs'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-600">
            <p class="text-gray-500 text-sm font-medium">Total Users</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-600">
            <p class="text-gray-500 text-sm font-medium">Total Comments</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_comments'] }}</p>
        </div>
    </div>

    {{-- All Blogs Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">All User Blogs</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Author</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Comments</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($blogs as $blog)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ Str::limit($blog->title, 40) }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $blog->user->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $blog->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($blog->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $blog->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $blog->comments_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.blog.show', $blog) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View & Comment</a>
                                    <form method="POST" action="{{ route('admin.blog.delete', $blog) }}" onsubmit="return confirm('Delete this blog?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">No blog posts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($blogs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>
</x-dashboard-layout>
