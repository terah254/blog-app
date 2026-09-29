<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BlogApp') }} - Discover Amazing Stories</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">

    {{-- Navigation --}}
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-3xl font-bold text-indigo-600">BlogApp</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-medium transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 font-medium transition">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <header class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight">Discover Amazing Stories</h1>
            <p class="text-xl md:text-2xl text-indigo-100 max-w-3xl mx-auto mb-8">Read, write, and share stories that matter. Join our community of passionate writers and readers today.</p>
            <div class="flex justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-bold text-lg hover:bg-gray-100 transition shadow-lg">Start Writing</a>
                    <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-bold text-lg hover:bg-white hover:text-indigo-600 transition">Sign In</a>
                @endguest
            </div>
        </div>
    </header>

    {{-- Stats Section --}}
    <section class="bg-white py-12 border-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-indigo-600">{{ $stats['published_blogs'] }}</div>
                    <div class="text-gray-600 mt-2">Published Articles</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-indigo-600">{{ $stats['total_users'] }}</div>
                    <div class="text-gray-600 mt-2">Active Writers</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-indigo-600">{{ $stats['total_comments'] }}</div>
                    <div class="text-gray-600 mt-2">Comments</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Search & Filter Section --}}
    <section class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('home') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search articles..." 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="md:w-48">
                    <select name="category" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->blogs_count }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium transition">
                    Search
                </button>
                @if(request('search') || request('category'))
                    <a href="{{ route('home') }}" class="px-6 py-2 border-2 border-gray-300 rounded-lg hover:border-gray-400 font-medium text-gray-700 transition text-center">
                        Clear
                    </a>
                @endif
            </form>
        </div>
    </section>

    {{-- Blog Grid --}}
    <main class="max-w-7xl mx-auto px-4 py-16">
        <div class="flex justify-between items-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800">
                @if(request('search'))
                    Search Results for "{{ request('search') }}"
                @elseif(request('category'))
                    Category: {{ $categories->firstWhere('slug', request('category'))?->name }}
                @else
                    Latest Articles
                @endif
            </h2>
            <div class="flex gap-2">
                <span class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">Latest</span>
                <span class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full text-sm font-medium cursor-pointer hover:bg-gray-300 transition">Popular</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($blogs as $blog)
                <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col">
                    <div class="h-56 bg-gradient-to-br from-indigo-400 to-purple-400 overflow-hidden relative">
                        @if($blog->image)
                            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white">
                                <svg class="w-20 h-20 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur rounded-full text-xs font-semibold text-indigo-600">
                                {{ ucfirst($blog->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                                {{ substr($blog->user->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ $blog->user->name }}</span>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $blog->title }}</h3>
                        <p class="text-gray-600 mb-4 flex-grow line-clamp-3">{{ Str::limit(strip_tags($blog->content), 120) }}</p>
                        
                        @if($blog->categories->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($blog->categories as $category)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <span class="text-sm text-gray-500">{{ $blog->created_at->format('M d, Y') }}</span>
                            <a href="{{ route('blogs.show', $blog) }}" class="text-indigo-600 font-semibold hover:text-indigo-800 transition flex items-center gap-1">
                                Read More
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-3 text-center py-20 bg-white rounded-2xl shadow">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <p class="text-gray-500 text-xl">No published blogs found.</p>
                    @if(request('search') || request('category'))
                        <a href="{{ route('home') }}" class="text-indigo-600 font-semibold hover:underline mt-2 inline-block">Clear filters</a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-16">
            {{ $blogs->withQueryString()->links() }}
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">BlogApp</h3>
                    <p class="text-gray-400">A platform for writers to share their stories and connect with readers worldwide.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Home</a></li>
                        <li><a href="#" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Categories</h4>
                    <ul class="space-y-2 text-gray-400">
                        @foreach($categories->take(3) as $category)
                            <li><a href="{{ route('home', ['category' => $category->slug]) }}" class="hover:text-white transition">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Connect</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Twitter</a></li>
                        <li><a href="#" class="hover:text-white transition">Facebook</a></li>
                        <li><a href="#" class="hover:text-white transition">LinkedIn</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} BlogApp. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
