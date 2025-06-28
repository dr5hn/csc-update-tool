<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeRequestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:3'
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:2000'
            ],
            'new_data' => [
                'required',
                'json'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your change request.',
            'title.min' => 'The title must be at least 3 characters long.',
            'title.max' => 'The title cannot exceed 255 characters.',
            'description.required' => 'Please provide a description explaining the changes.',
            'description.min' => 'The description must be at least 10 characters long.',
            'description.max' => 'The description cannot exceed 2000 characters.',
            'new_data.required' => 'Change data is required.',
            'new_data.json' => 'Change data must be valid JSON format.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure new_data is properly formatted
        if ($this->has('new_data') && is_array($this->new_data)) {
            $this->merge([
                'new_data' => json_encode($this->new_data)
            ]);
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'new_data' => 'change data',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }
}
