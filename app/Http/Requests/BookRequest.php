<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book');
        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'author' => 'sometimes|required|string|max:255',
            'isbn' => 'sometimes|required|string|max:13|unique:books,isbn,' . $bookId,
            'published_year' => 'sometimes|required|integer|min:1000|max:' . (date('Y') + 1),
            'category_id' => 'sometimes|required|exists:categories,id',
            'total_copies' => 'sometimes|required|integer|min:0',
            'available_copies' => 'sometimes|required|integer|min:0|lte:total_copies',
        ];

        // Only apply rules for fields that are present in the request
        return array_intersect_key($rules, $this->all());
    }

    protected function failedValidation(Validator $validator)
    {
        Log::warning('Book validation failed', [
            'errors' => $validator->errors()->toArray(),
            'request_data' => $this->all()
        ]);

        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors()
        ], 422));
    }

    protected function prepareForValidation()
    {
        // Handle malformed JSON
        if ($this->isJson() && json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpResponseException(response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['json' => ['The provided JSON is malformed.']]
            ], 422));
        }
    }

    protected function passedValidation()
    {
        // If you need to modify the validated data, you can do it here
        // For example, to ensure 'published_year' is not in the future:
        if ($this->has('published_year')) {
            $this->merge([
                'published_year' => min($this->published_year, date('Y') + 1)
            ]);
        }
    }
}
