<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Product;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        $toyCar = Product::where('name', 'Toy Car')->first();

        // Delivery Method
        $delivery = Attribute::create([
            'name' => 'Delivery Method',
            'type' => 'delivery',
        ]);

        $atHome = AttributeOption::create([
            'attribute_id' => $delivery->id,
            'name' => 'At Home',
            'price' => 20.00,
        ]);

        $inLab = AttributeOption::create([
            'attribute_id' => $delivery->id,
            'name' => 'In Lab',
            'price' => 0.00,
        ]);

        // Speed
        $speed = Attribute::create([
            'name' => 'Delivery Speed',
            'type' => 'speed',
        ]);

        $sameDay = AttributeOption::create([
            'attribute_id' => $speed->id,
            'name' => 'Same Day',
            'price' => 30.00,
        ]);

        $nextDay = AttributeOption::create([
            'attribute_id' => $speed->id,
            'name' => 'Next Day',
            'price' => 10.00,
        ]);

        // Associate all options to Toy Car
        $toyCar->attributeOptions()->attach([
            $atHome->id,
            $inLab->id,
            $sameDay->id,
            $nextDay->id,
        ]);
    }
}
