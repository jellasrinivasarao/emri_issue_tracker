<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class IssueCategoryRequest extends FormRequest
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

        'category_code' =>
                'required|max:50|unique:mst_issue_category,category_code,' .
                $this->issueCategory?->issue_category_id .
                ',issue_category_id',


            'category_name' =>
                'required|max:150',


            'description' =>
                'nullable|string',


            'is_active' => ['nullable', 'boolean']
            
        ];
    }

    public function attributes(): array
    {
        return [
            'category_code' => 'Category Code',
            'category_name' => 'Category Name',
            'description' => 'Description',
        ];
    }
}