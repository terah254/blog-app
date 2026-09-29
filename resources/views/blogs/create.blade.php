<x-dashboard-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-8">
                    <div class="flex items-center mb-8">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Create New Blog Post</h2>
                            <p class="text-gray-500 mt-1">Share your story with the world</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('blogs.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Blog Title *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Enter an engaging title..."
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg @error('title') border-red-500 @enderror">
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Featured Image</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-500 transition cursor-pointer" onclick="document.getElementById('image').click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <span class="font-medium text-indigo-600 hover:text-indigo-500">Upload a file</span>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                            <div id="imagePreview" class="mt-4 hidden">
                                <img id="preview" class="h-48 w-auto rounded-lg shadow-md">
                            </div>
                            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">Content *</label>
                            <textarea name="content" id="content" rows="15" required
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                            @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Categories</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($categories as $category)
                                    <label class="inline-flex items-center px-4 py-2 rounded-full border border-gray-300 hover:border-indigo-500 cursor-pointer transition">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                               {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-8">
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Publish Status</label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" name="status" value="published" {{ old('status') === 'published' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-gray-700">Publish immediately</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status" value="draft" {{ old('status') === 'draft' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-gray-700">Save as draft</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold text-lg shadow-lg hover:shadow-xl transition">
                                Publish Blog
                            </button>
                            <a href="{{ route('user.dashboard') }}" class="px-6 py-3 border-2 border-gray-300 rounded-lg hover:border-gray-400 font-semibold text-gray-700 transition">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#content',
                    height: 500,
                    menubar: true,
                    branding: false,
                    promotion: false,
                    skin: 'oxide',
                     
                    plugins: [
                        'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 
                        'image', 'link', 'lists', 'media', 'searchreplace', 'table', 
                        'visualblocks', 'wordcount', 'fullscreen', 'preview', 'code'
                    ],
                     
                    toolbar: 'undo redo | blocks fontfamily fontsize | ' +
                             'bold italic underline strikethrough | forecolor backcolor | ' +
                             'alignleft aligncenter alignright alignjustify | ' +
                             'bullist numlist outdent indent | ' +
                             'link image media table codesample | ' +
                             'removeformat | fullscreen preview code',
                              
                    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333; }',
                     
                    setup: function(editor) {
                        editor.on('change', function() {
                            editor.save();
                        });
                    }
                });
            } else {
                console.error('TinyMCE failed to load!');
            }
        });

        function previewImage(input) {
            for (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview');
                    const previewDiv = document.getElementById('imagePreview');
                    if(preview && previewDiv) {
                        preview.src = e.target.result;
                        previewDiv.classList.remove('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-dashboard-layout>
