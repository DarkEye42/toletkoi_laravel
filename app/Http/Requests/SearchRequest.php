<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'listing_type' => ['nullable', 'in:rent,sale,sublet,shared'],
            'property_type' => ['nullable', 'integer', 'exists:property_types,id'],
            'property_type_id' => ['nullable', 'integer', 'exists:property_types,id'],
            'division' => ['nullable', 'integer', 'exists:divisions,id'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'district' => ['nullable', 'integer', 'exists:districts,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'area' => ['nullable', 'integer', 'exists:areas,id'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'subtype_id' => ['nullable', 'integer', 'exists:property_subtypes,id'],
            'furnishing_status' => ['nullable', 'in:unfurnished,semi_furnished,fully_furnished'],
            'min_area' => ['nullable', 'numeric', 'min:0'],
            'max_area' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:newest,price_low,price_high,popular'],
            'page' => ['nullable', 'integer', 'min:1'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'zoom' => ['nullable', 'integer', 'min:1', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'property_type_id.exists' => 'Selected property type is invalid.',
            'division_id.exists' => 'Selected division is invalid.',
            'district_id.exists' => 'Selected district is invalid.',
            'area_id.exists' => 'Selected area is invalid.',
            'budget_max.gte' => 'Maximum budget must be greater than or equal to minimum budget.',
            'max_price.gte' => 'Maximum price must be greater than or equal to minimum price.',
            'bedrooms.min' => 'Number of bedrooms cannot be negative.',
            'bathrooms.min' => 'Number of bathrooms cannot be negative.',
            'furnishing_status.in' => 'Invalid furnishing status.',
            'listing_type.in' => 'Invalid listing type.',
            'sort.in' => 'Invalid sort option.',
        ];
    }
}
