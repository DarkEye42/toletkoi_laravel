<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;
use App\Models\PropertySubtype;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $propertyTypes = [
            [
                'name' => 'Apartment',
                'name_bn' => 'অ্যাপার্টমেন্ট',
                'slug' => 'apartment',
                'icon' => 'apartment',
                'description' => 'Individual apartment units in residential buildings',
                'is_active' => true,
                'sort_order' => 1,
                'subtypes' => [
                    ['name' => 'Studio Apartment', 'name_bn' => 'স্টুডিও অ্যাপার্টমেন্ট', 'slug' => 'studio-apartment'],
                    ['name' => '1 BHK Apartment', 'name_bn' => '১ বিহেকে অ্যাপার্টমেন্ট', 'slug' => '1-bhk-apartment'],
                    ['name' => '2 BHK Apartment', 'name_bn' => '২ বিহেকে অ্যাপার্টমেন্ট', 'slug' => '2-bhk-apartment'],
                    ['name' => '3 BHK Apartment', 'name_bn' => '৩ বিহেকে অ্যাপার্টমেন্ট', 'slug' => '3-bhk-apartment'],
                    ['name' => '4+ BHK Apartment', 'name_bn' => '৪+ বিহেকে অ্যাপার্টমেন্ট', 'slug' => '4-plus-bhk-apartment'],
                ],
            ],
            [
                'name' => 'House',
                'name_bn' => 'বাড়ি',
                'slug' => 'house',
                'icon' => 'house',
                'description' => 'Independent residential houses',
                'is_active' => true,
                'sort_order' => 2,
                'subtypes' => [
                    ['name' => 'Single Family Home', 'name_bn' => 'এক পরিবারের বাড়ি', 'slug' => 'single-family-home'],
                    ['name' => 'Duplex', 'name_bn' => 'ডুপ্লেক্স', 'slug' => 'duplex'],
                    ['name' => 'Townhouse', 'name_bn' => 'টাউনহাউস', 'slug' => 'townhouse'],
                    ['name' => 'Bungalow', 'name_bn' => 'বাংলো', 'slug' => 'bungalow'],
                ],
            ],
            [
                'name' => 'Room',
                'name_bn' => 'রুম',
                'slug' => 'room',
                'icon' => 'room',
                'description' => 'Individual rooms in shared spaces',
                'is_active' => true,
                'sort_order' => 3,
                'subtypes' => [
                    ['name' => 'Single Room', 'name_bn' => 'একক রুম', 'slug' => 'single-room'],
                    ['name' => 'Shared Room', 'name_bn' => 'ভাগাভাগি রুম', 'slug' => 'shared-room'],
                    ['name' => 'Master Bedroom', 'name_bn' => 'মাস্টার বেডরুম', 'slug' => 'master-bedroom'],
                ],
            ],
            [
                'name' => 'Office Space',
                'name_bn' => 'অফিস স্পেস',
                'slug' => 'office-space',
                'icon' => 'office',
                'description' => 'Commercial office spaces',
                'is_active' => true,
                'sort_order' => 4,
                'subtypes' => [
                    ['name' => 'Private Office', 'name_bn' => 'ব্যক্তিগত অফিস', 'slug' => 'private-office'],
                    ['name' => 'Co-working Space', 'name_bn' => 'কো-ওয়ার্কিং স্পেস', 'slug' => 'co-working-space'],
                    ['name' => 'Virtual Office', 'name_bn' => 'ভার্চুয়াল অফিস', 'slug' => 'virtual-office'],
                ],
            ],
            [
                'name' => 'Shop',
                'name_bn' => 'দোকান',
                'slug' => 'shop',
                'icon' => 'shop',
                'description' => 'Retail and commercial shop spaces',
                'is_active' => true,
                'sort_order' => 5,
                'subtypes' => [
                    ['name' => 'Retail Shop', 'name_bn' => 'রিটেইল দোকান', 'slug' => 'retail-shop'],
                    ['name' => 'Showroom', 'name_bn' => 'শোরুম', 'slug' => 'showroom'],
                    ['name' => 'Kiosk', 'name_bn' => 'কিওস্ক', 'slug' => 'kiosk'],
                ],
            ],
            [
                'name' => 'Warehouse',
                'name_bn' => 'গুদাম',
                'slug' => 'warehouse',
                'icon' => 'warehouse',
                'description' => 'Storage and warehouse facilities',
                'is_active' => true,
                'sort_order' => 6,
                'subtypes' => [
                    ['name' => 'Small Warehouse', 'name_bn' => 'ছোট গুদাম', 'slug' => 'small-warehouse'],
                    ['name' => 'Large Warehouse', 'name_bn' => 'বড় গুদাম', 'slug' => 'large-warehouse'],
                    ['name' => 'Cold Storage', 'name_bn' => 'কোল্ড স্টোরেজ', 'slug' => 'cold-storage'],
                ],
            ],
            [
                'name' => 'Land',
                'name_bn' => 'জমি',
                'slug' => 'land',
                'icon' => 'land',
                'description' => 'Land plots for sale or lease',
                'is_active' => true,
                'sort_order' => 7,
                'subtypes' => [
                    ['name' => 'Residential Land', 'name_bn' => 'আবাসিক জমি', 'slug' => 'residential-land'],
                    ['name' => 'Commercial Land', 'name_bn' => 'বাণিজ্যিক জমি', 'slug' => 'commercial-land'],
                    ['name' => 'Agricultural Land', 'name_bn' => 'কৃষি জমি', 'slug' => 'agricultural-land'],
                ],
            ],
            [
                'name' => 'Parking Space',
                'name_bn' => 'পার্কিং স্পেস',
                'slug' => 'parking-space',
                'icon' => 'parking',
                'description' => 'Parking spots and garages',
                'is_active' => true,
                'sort_order' => 8,
                'subtypes' => [
                    ['name' => 'Covered Parking', 'name_bn' => 'চালানো পার্কিং', 'slug' => 'covered-parking'],
                    ['name' => 'Open Parking', 'name_bn' => 'খোলা পার্কিং', 'slug' => 'open-parking'],
                    ['name' => 'Garage', 'name_bn' => 'গ্যারেজ', 'slug' => 'garage'],
                ],
            ],
            [
                'name' => 'Factory',
                'name_bn' => 'কারখানা',
                'slug' => 'factory',
                'icon' => 'factory',
                'description' => 'Industrial factory and manufacturing spaces',
                'is_active' => true,
                'sort_order' => 9,
                'subtypes' => [
                    ['name' => 'Small Factory', 'name_bn' => 'ছোট কারখানা', 'slug' => 'small-factory'],
                    ['name' => 'Large Factory', 'name_bn' => 'বড় কারখানা', 'slug' => 'large-factory'],
                    ['name' => 'Industrial Plot', 'name_bn' => 'শিল্প জমি', 'slug' => 'industrial-plot'],
                ],
            ],
            [
                'name' => 'Commercial Building',
                'name_bn' => 'বাণিজ্যিক ভবন',
                'slug' => 'commercial-building',
                'icon' => 'commercial',
                'description' => 'Full commercial buildings for lease',
                'is_active' => true,
                'sort_order' => 10,
                'subtypes' => [
                    ['name' => 'Office Building', 'name_bn' => 'অফিস ভবন', 'slug' => 'office-building'],
                    ['name' => 'Shopping Mall', 'name_bn' => 'শপিং মল', 'slug' => 'shopping-mall'],
                    ['name' => 'Mixed Use Building', 'name_bn' => 'মিশ্র ব্যবহারের ভবন', 'slug' => 'mixed-use-building'],
                ],
            ],
        ];

        foreach ($propertyTypes as $typeData) {
            $subtypes = $typeData['subtypes'];
            unset($typeData['subtypes']);

            $type = PropertyType::updateOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );

            foreach ($subtypes as $subtypeData) {
                PropertySubtype::updateOrCreate(
                    ['slug' => $subtypeData['slug']],
                    array_merge($subtypeData, ['property_type_id' => $type->id])
                );
            }
        }

        $this->command->info('Property types and subtypes seeded successfully!');
    }
}
