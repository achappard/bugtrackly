<?php

namespace App\Http\Requests\Settings\Projects;

use Illuminate\Foundation\Http\FormRequest;

class DeleteProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-projects');
    }
}
