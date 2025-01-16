<?php

namespace App\Http\Requests;

use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rules\File;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $post = $this->route('post');
        return $this->user()->can('update', $post);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes'],
            'content' => ['sometimes'],
            'category' => ['sometimes', 'exists:categories,id'],
            'image' => ['nullable', 'sometimes', function ($attribute, $value, $fail) {
                if (is_string($value) && !is_file($value)) {
                    return;  // Allow string paths for existing images
                }
                // Apply file validation only for new uploads
                if ($value instanceof UploadedFile) {
                    return Validator::make(
                        [$attribute => $value],
                        [$attribute => File::types(['jpg', 'jpeg', 'png', 'svg'])->max(2048)]
                    )->validate();
                }
                $fail("Error On Image...");
            }],
        ];
    }
}
