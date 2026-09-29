<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'required|in:published,draft',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required'   => 'A blog title is required.',
            'title.max'        => 'The title cannot exceed 255 characters.',
            'content.required' => 'Blog content is required.',
            'status.required'  => 'Please select a publish status.',
            'status.in'        => 'Status must be either published or draft.',
            'image.image'      => 'The file must be an image.',
            'image.mimes'      => 'The image must be a JPEG, PNG, JPG, GIF, or WebP file.',
            'image.max'        => 'The image cannot be larger than 2MB.',
        ];
    }
}
