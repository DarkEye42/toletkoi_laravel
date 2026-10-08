<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $listingId = $this->route('listing')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'title_bn' => ['nullable', 'string', 'max:255'],
            'property_type_id' => ['required', 'exists:property_types,id'],
            'subtype_id' => ['nullable', 'exists:property_subtypes,id'],
            'listing_type' => ['required', 'in:rent,sale,sublet,shared'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_type' => ['nullable', 'in:monthly,yearly,one_time'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'balconies' => ['nullable', 'integer', 'min:0', 'max:10'],
            'floor_level' => ['nullable', 'integer'],
            'total_floors' => ['nullable', 'integer'],
            'area_sqft' => ['nullable', 'numeric', 'min:0'],
            'furnishing_status' => ['nullable', 'in:unfurnished,semi_furnished,fully_furnished'],
            'description' => ['required', 'string', 'max:10000'],
            'description_bn' => ['nullable', 'string', 'max:10000'],
            'address' => ['required', 'string', 'max:500'],
            'division_id' => ['required', 'exists:divisions,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'available_from' => ['nullable', 'date', 'after_or_equal:today'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            // Feature flags
            'has_parking' => ['nullable', 'boolean'],
            'has_lift' => ['nullable', 'boolean'],
            'has_generator' => ['nullable', 'boolean'],
            'has_cctv' => ['nullable', 'boolean'],
            'has_security' => ['nullable', 'boolean'],
            'has_swimming_pool' => ['nullable', 'boolean'],
            'has_gym' => ['nullable', 'boolean'],
            'has_rooftop' => ['nullable', 'boolean'],
            'has_balcony' => ['nullable', 'boolean'],
            'has_garden' => ['nullable', 'boolean'],
            'has_internet' => ['nullable', 'boolean'],
            'has_gas' => ['nullable', 'boolean'],
            'has_water_supply' => ['nullable', 'boolean'],
            'pet_friendly' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a property title.',
            'property_type_id.required' => 'Please select a property type.',
            'property_type_id.exists' => 'Selected property type is invalid.',
            'listing_type.required' => 'Please select listing type (Rent/Sale/Sublet/Shared).',
            'price.required' => 'Please enter the price.',
            'price.min' => 'Price cannot be negative.',
            'description.required' => 'Please provide a property description.',
            'address.required' => 'Please enter the property address.',
            'division_id.required' => 'Please select a division.',
            'district_id.required' => 'Please select a district.',
            'images.max' => 'You can upload a maximum of 6 images.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be JPEG, PNG, JPG, GIF, or WebP.',
            'images.*.max' => 'Each image must be under 5MB.',
            'available_from.after_or_equal' => 'Availability date cannot be in the past.',
        ];
    }
}
