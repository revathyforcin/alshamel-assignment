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
    public $userType = 'normal'; // or 'company' - use any by default
    public $price;

    public $selectedProduct; 
    public $selectedAttributeOptionsForDisplay = [];

    const AT_HOME_OPTION_ID = 5;


    public function mount()
    {
        // Load all products with their associated attributes and options
        $this->products = Product::with('attributeOptions.attribute')->get();
        
        // Set the initially selected product to the first one in the list (if any)
        $this->selectedProductId = $this->products->first()?->id;
        
        // Load the details of the selected product
        $this->loadSelectedProduct();
    }

    public function updatedSelectedProductId()
    {
        // When the selected product ID changes (e.g., user selects a different product),
        // reload the selected product's details
        $this->loadSelectedProduct();
    }

    public function updatedSelectedOptions()
    {
        // Recalculate the final price based on the new selected options
        $this->calculatePrice();
    }

    public function updatedUserType()
    {
        // Recalculate the final price based on the new user type
        $this->calculatePrice();
    }

    protected function loadSelectedProduct()
    {
        // Find and set the selected product object based on selectedProductId
        $this->selectedProduct = $this->products->firstWhere('id', $this->selectedProductId);
        
        // Reset selected options whenever the product changes
        $this->selectedOptions = []; 
        // Reset selected attribute options used for display purposes
        $this->selectedAttributeOptionsForDisplay = [];
        if ($this->selectedProduct) {
            // Initialize the selectedOptions array keys with attribute names, setting their values to empty strings
            // to store user's attribute selections
            foreach ($this->selectedProduct->attributeOptions->groupBy('attribute.name') as $attributeName => $options) {
                $this->selectedOptions[$attributeName] = ''; 
            }
        }
        // After resetting and initializing selections, recalculate the price
        $this->calculatePrice(); 
    }

    public function calculatePrice()
    {
        // If no product is selected, clear the price and exit early
        if (!$this->selectedProduct) {
            $this->price = null;
            return;
        }

        // Get base price of the selected product
        $basePrice = $this->selectedProduct->base_price ?? 0;
        // Initialize total of attribute price additions
        $attributeTotal = 0;
        // Clear previously stored attribute options for display, to update fresh
        $this->selectedAttributeOptionsForDisplay = [];

        // Calculate total price additions for selected attribute options
        foreach ($this->selectedOptions as $attributeName => $optionId) {
            if ($optionId) {
                // Load the full AttributeOption with its related Attribute (for display & price)
                $option = AttributeOption::with('attribute')->find($optionId);
                if ($option) {
                    // Add the option's price to the attribute total
                    $attributeTotal += $option->price ?? 0;
                    // Save this option for displaying selected attributes to user
                    $this->selectedAttributeOptionsForDisplay[] = $option;
                }
            }
        }

        // Calculate subtotal before discounts (base price + all attribute price additions)
        $currentTotal = $basePrice + $attributeTotal;
        $originalSubtotal = $currentTotal;

        // Initialize discounts tracking array for each discount type applied
        $discountsApplied = [
            'attribute' => 0, 
            'subtotal' => 0,
            'user_type' => 0,
            'at_home_option' => 0, 
        ];
        // Attribute-based Discounts
        $attributeDiscounts = DiscountRule::where('type', 'attribute')->get();
        foreach ($attributeDiscounts as $rule) {
            // Check if the discount rule's condition matches any selected option IDs
            if (in_array($rule->condition, array_values($this->selectedOptions))) {
                // Skip the special 'At Home' 5% discount here as it's handled separately below
                if ($rule->condition == self::AT_HOME_OPTION_ID && $rule->discount_type == 'percent' && $rule->value == 5) {
                    continue; 
                }

                // Calculate discount amount based on type (percent or fixed)
                $discountAmount = $rule->discount_type === 'percent'
                    ? ($currentTotal * $rule->value / 100)
                    : $rule->value;
                // Accumulate attribute discounts
                $discountsApplied['attribute'] += $discountAmount;
            }
        }
        // Subtract attribute-based discounts from current total, ensuring not below zero
        $currentTotal = max(0, $currentTotal - $discountsApplied['attribute']);


        // Apply other discounts based on user type 
        if ($this->userType === 'company') {
            // Company user order: User Type -> At Home -> Subtotal-based

            // Company User Type Discount (20%)
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
            
            // "At Home" Option Specific Discount (5%, calculated on currentTotal)
            if (in_array(self::AT_HOME_OPTION_ID, array_values($this->selectedOptions))) {
                $atHomeDiscountPercentage = 5; 
                $discountAmount = ($currentTotal * $atHomeDiscountPercentage / 100);
                $discountsApplied['at_home_option'] += $discountAmount;
                $currentTotal = max(0, $currentTotal - $discountsApplied['at_home_option']);
            }

            // Subtotal-based Discounts (e.g., "10 Over 130") - last for company
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
            // "At Home" Option Specific Discount (5%, calculated on currentTotal)
            if (in_array(self::AT_HOME_OPTION_ID, array_values($this->selectedOptions))) {
                $atHomeDiscountPercentage = 5; 
                $discountAmount = ($currentTotal * $atHomeDiscountPercentage / 100);
                $discountsApplied['at_home_option'] += $discountAmount;
                $currentTotal = max(0, $currentTotal - $discountsApplied['at_home_option']);
            }

            // Subtotal-based Discounts (e.g., "10 Over 130") - after At Home for normal
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
        }

        // Store the full breakdown and final price in the $price property
        $this->price = [
            'base' => $basePrice,
            'attributeTotal' => $attributeTotal,
            'subtotal' => $originalSubtotal,
            'discounts' => $discountsApplied,
            'final' => $currentTotal,
        ];
    }

    public function render()
    {
        return view('livewire.product-configurator');
    }
}