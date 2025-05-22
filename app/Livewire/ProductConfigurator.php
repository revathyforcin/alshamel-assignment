<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\AttributeOption;
use App\Models\DiscountRule;
use Livewire\Component;

class ProductConfigurator extends Component
{
    public $products;
    public $selectedProductId;
    public $selectedOptions = []; 
    public $userType = 'normal'; // or 'company'
    public $price;

    public $selectedProduct; // Cached selected product model
    public $selectedAttributeOptionsForDisplay = []; // <-- NEW PUBLIC PROPERTY

    // Define the specific option IDs that trigger discounts or are relevant for calculations
    // YOU MUST REPLACE THESE WITH THE ACTUAL IDs FROM YOUR DATABASE
    const AT_HOME_OPTION_ID = 5;   // Example ID for 'At Home' option
    // You might need an ID for 'Same Day' if it's special, but for now we'll just list all selected.
    // const SAME_DAY_OPTION_ID = 6;  // Example ID for 'Same Day' option (if needed for special display)


    public function mount()
    {
        $this->products = Product::with('attributeOptions.attribute')->get();
        $this->selectedProductId = $this->products->first()?->id;
        $this->loadSelectedProduct();
    }

    public function updatedSelectedProductId()
    {
        $this->loadSelectedProduct();
    }

    public function updatedSelectedOptions()
    {
        logger('updatedSelectedOptions detected change. Recalculating price.');
        $this->calculatePrice();
    }

    public function updatedUserType()
    {
        logger('updatedUserType detected change. Recalculating price.');
        $this->calculatePrice();
    }

    protected function loadSelectedProduct()
    {
        $this->selectedProduct = $this->products->firstWhere('id', $this->selectedProductId);
        
        $this->selectedOptions = []; // Always reset options when product changes
        $this->selectedAttributeOptionsForDisplay = []; // <-- RESET NEW PROPERTY
        if ($this->selectedProduct) {
            foreach ($this->selectedProduct->attributeOptions->groupBy('attribute.name') as $attributeName => $options) {
                $this->selectedOptions[$attributeName] = ''; 
            }
        }
        $this->calculatePrice(); // Recalculate price after product change
    }

    public function calculatePrice()
    {
        if (!$this->selectedProduct) {
            $this->price = null;
            return;
        }

        $basePrice = $this->selectedProduct->base_price ?? 0;
        $attributeTotal = 0;
        $this->selectedAttributeOptionsForDisplay = []; // Clear for new calculation cycle
        logger('calculatePrice called: selectedOptions state: ' . json_encode($this->selectedOptions) . ', userType: ' . $this->userType);

        // Calculate total for selected attributes AND store for display
        foreach ($this->selectedOptions as $attributeName => $optionId) {
            if ($optionId) {
                $option = AttributeOption::with('attribute')->find($optionId); // Eager load attribute for display
                if ($option) {
                    $attributeTotal += $option->price ?? 0;
                    $this->selectedAttributeOptionsForDisplay[] = $option; // Store the full option object
                }
            }
        }

        $currentTotal = $basePrice + $attributeTotal;
        $originalSubtotal = $currentTotal; // Keep track of the subtotal before any discounts

        $discountsApplied = [
            'attribute' => 0, // This will capture any OTHER attribute-based discounts
            'subtotal' => 0,
            'user_type' => 0,
            'at_home_option' => 0, 
        ];

        // --- 1. Attribute-based Discounts (applies always, first) ---
        // This loop applies to any discount rules of type 'attribute' that are NOT 'at_home_option' if you have them.
        // The specific 'at_home_option' discount is handled separately below.
        $attributeDiscounts = DiscountRule::where('type', 'attribute')->get();
        foreach ($attributeDiscounts as $rule) {
            if (in_array($rule->condition, array_values($this->selectedOptions))) {
                // Ensure this rule isn't the specific 'at_home_option' percentage one if it's defined as 'attribute' type
                // (e.g., if you had a DiscountRule where type='attribute', condition=AT_HOME_OPTION_ID, discount_type='percent', value=5)
                // We handle the 5% At Home discount explicitly in its own section.
                // If this is *only* for the 5% at home, this `if` block below could be removed.
                // For robustness, I'll keep it, assuming there might be other attribute discounts.
                // If AT_HOME_OPTION_ID discount rule is *only* 5% percent discount, then we need to filter that specific one out here.
                 if ($rule->condition == self::AT_HOME_OPTION_ID && $rule->discount_type == 'percent' && $rule->value == 5) {
                    // Skip if this is the 5% At Home discount, as it's handled in its dedicated section
                    continue; 
                }

                $discountAmount = $rule->discount_type === 'percent'
                    ? ($currentTotal * $rule->value / 100)
                    : $rule->value;
                $discountsApplied['attribute'] += $discountAmount;
            }
        }
        $currentTotal = max(0, $currentTotal - $discountsApplied['attribute']);


        // --- Apply other discounts based on user type order ---
        if ($this->userType === 'company') {
            // Company user order: User Type -> At Home -> Subtotal-based

            // 2. Company User Type Discount (20%)
            $companyDiscountRule = DiscountRule::where('type', 'user_type')
                                            ->where('condition', 'company')
                                            ->first();
            if ($companyDiscountRule) {
                $discountAmount = $companyDiscountRule->discount_type === 'percent'
                    ? ($currentTotal * $companyDiscountRule->value / 100)
                    : $companyDiscountRule->value;
                $discountsApplied['user_type'] += $discountAmount;
                $currentTotal = max(0, $currentTotal - $discountsApplied['user_type']);
            }
            
            // 3. "At Home" Option Specific Discount (5%, calculated on currentTotal)
            if (in_array(self::AT_HOME_OPTION_ID, array_values($this->selectedOptions))) {
                $atHomeDiscountPercentage = 5; 
                $discountAmount = ($currentTotal * $atHomeDiscountPercentage / 100);
                $discountsApplied['at_home_option'] += $discountAmount;
                $currentTotal = max(0, $currentTotal - $discountsApplied['at_home_option']);
            }

            // 4. Subtotal-based Discounts (e.g., "10 Over 130") - last for company
            $subtotalDiscounts = DiscountRule::where('type', 'subtotal')->get();
            foreach ($subtotalDiscounts as $rule) {
                if ($currentTotal >= $rule->condition) { 
                    $discountAmount = $rule->discount_type === 'percent'
                        ? ($currentTotal * $rule->value / 100)
                        : $rule->value;
                    $discountsApplied['subtotal'] += $discountAmount;
                }
            }
            $currentTotal = max(0, $currentTotal - $discountsApplied['subtotal']);

        } else { // Covers 'normal' and any other user types
            // Normal user order: At Home -> Subtotal-based (no generic user type discount for 'normal')

            // 2. "At Home" Option Specific Discount (5%, calculated on currentTotal)
            if (in_array(self::AT_HOME_OPTION_ID, array_values($this->selectedOptions))) {
                $atHomeDiscountPercentage = 5; 
                $discountAmount = ($currentTotal * $atHomeDiscountPercentage / 100);
                $discountsApplied['at_home_option'] += $discountAmount;
                $currentTotal = max(0, $currentTotal - $discountsApplied['at_home_option']);
            }

            // 3. Subtotal-based Discounts (e.g., "10 Over 130") - after At Home for normal
            $subtotalDiscounts = DiscountRule::where('type', 'subtotal')->get();
            foreach ($subtotalDiscounts as $rule) {
                if ($currentTotal >= $rule->condition) { 
                    $discountAmount = $rule->discount_type === 'percent'
                        ? ($currentTotal * $rule->value / 100)
                        : $rule->value;
                    $discountsApplied['subtotal'] += $discountAmount;
                }
            }
            $currentTotal = max(0, $currentTotal - $discountsApplied['subtotal']);

            // No specific 'user_type' discount for 'normal' in this scenario.
        }

        $this->price = [
            'base' => $basePrice,
            'attributeTotal' => $attributeTotal, // Sum of selected attribute prices
            'subtotal' => $originalSubtotal, // Base + all attribute prices
            'discounts' => $discountsApplied,
            'final' => $currentTotal,
        ];
    }

    public function render()
    {
        // $this->selectedAttributeOptionsForDisplay is public, so it's available in the view automatically
        return view('livewire.product-configurator');
    }
}