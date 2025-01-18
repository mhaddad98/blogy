<?php

namespace App\Http\Requests;

use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class UpdateDraftPostRequest extends FormRequest
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
            'content' => ['sometimes',],
            'category' => ['sometimes', 'nullable', 'exists:categories,id'],
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $nonEmptyFields = array_filter($this->only(['title', 'content', 'category', 'image']), function ($value) {
                return !is_null($value) && $value !== '';
            });

            if (empty($nonEmptyFields)) {
                $validator->errors()->add('main', 'At least one field must be provided to store a draft.');
            }
        });
    }
}
