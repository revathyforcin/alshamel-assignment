<?php

namespace Database\Seeders;

use App\Models\AttributeOption;
use App\Models\DiscountRule;
use Illuminate\Database\Seeder;

class DiscountRuleSeeder extends Seeder
{
    public function run()
    {
        // 5% off if Delivery = At Home
        $atHomeOption = AttributeOption::where('name', 'At Home')->first();
        DiscountRule::create([
            'type' => 'attribute',
            'condition' => $atHomeOption->id, // match with selected option ID
            'value' => 5, // 5%
            'discount_type' => 'percent',
        ]);

        // 10 KD off if subtotal > 130
        DiscountRule::create([
            'type' => 'subtotal',
            'condition' => 130,
            'value' => 10, // fixed
            'discount_type' => 'fixed',
        ]);

        // 20% off for user type = company
        DiscountRule::create([
            'type' => 'user_type',
            'condition' => 'company',
            'value' => 20,
            'discount_type' => 'percent',
        ]);
    }
}
