<?php

namespace App\Http\Requests\Settings\Projects;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-projects');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'short_desc' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            'delete_old_photo' => ['required', 'boolean'],
            'users' => ['array'],
            'users.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'delete_old_photo' => filter_var($this->delete_old_photo, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
