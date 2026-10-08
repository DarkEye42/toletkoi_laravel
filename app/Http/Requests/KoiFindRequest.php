<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KoiFindRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'property_type_id' => ['nullable', 'exists:property_types,id'],
            'listing_type' => ['required', 'in:rent,sale'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'preferred_areas' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please give your search a title.',
            'listing_type.required' => 'Please select rent or sale.',
            'budget_max.gte' => 'Maximum budget must be greater than or equal to minimum budget.',
        ];
    }
}
