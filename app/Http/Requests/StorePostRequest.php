<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'content' => ['required',],
            'category' => ['required', 'exists:categories,id'],
            // 'image' => ['nullable', File::types(['jpg', 'jpeg', 'png', 'svg'])->max(2048)],
            'image' => ['nullable', 'sometimes', function ($attribute, $value, $fail) {
                if (is_string($value) && !is_file($value)) {
                    return;
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
