<div class="flex gap-4">
    <div class="w-full md:w-[70%]">
        <h2 class="text-2xl font-bold mb-4">Shop</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($products as $product)
                <div 
                    class="border rounded p-4 shadow cursor-pointer hover:border-blue-500 {{ $selectedProductId == $product->id ? 'border-blue-500' : '' }}" 
                    wire:click="$set('selectedProductId', {{ $product->id }})"
                >
                    <img 
                        src="{{ $product->image_url ?? asset('images/toycar.jpg') }}" 
                        alt="{{ $product->name }}" 
                        class="w-full h-48 object-cover mb-2 rounded"
                    >
                    <h3 class="text-lg font-bold mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-600">Price: ₹{{ $product->base_price }}</p>

                    @if ($selectedProductId == $product->id)
                        <span class="text-sm text-blue-600 font-semibold">
                            
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="w-full md:w-[30%]">


    @if ($selectedProduct)
        <div class="p-4 border rounded bg-gray-50">
            <h2 class="text-xl font-semibold mb-4">Price Breakup: {{ $selectedProduct->name }}</h2>

            <div class="mb-4">
                <label class="block font-medium mb-1">User Type:</label>
                <select wire:model.live="userType" class="border p-2 w-full">
                    <option value="normal">Normal</option>
                    <option value="company">Company</option>
                </select>
            </div>
            
            @foreach ($selectedProduct->attributeOptions->groupBy('attribute.name') as $attributeName => $options)
                <div class="mb-4">
                    <label class="block font-medium">{{ $attributeName }}</label>
                    <select wire:model.live="selectedOptions.{{ $attributeName }}" class="w-full border p-2">
                        <option value="">-- Choose Option --</option>
                        @foreach ($options as $option)
                            <option value="{{ $option->id }}">{{ $option->name }} (+₹{{ $option->price }})</option>
                        @endforeach
                    </select>
                    {{-- <pre>@json($selectedOptions)</pre> This is for debugging, remove in production --}}
                </div>
            @endforeach

            {{-- PRICE DETAILS (Updated to match screenshot) --}}
            @if ($price)
                <div class="mt-4">
                    <h3 class="font-semibold">Price Details</h3>
                    <p>Base: ₹{{ number_format($price['base'], 2) }}</p>

                    @foreach ($selectedAttributeOptionsForDisplay as $option)
                        <p class="ml-4">+ {{ $option->name }}: ₹{{ number_format($option->price, 2) }}</p>
                    @endforeach

                    <p class="font-bold">Subtotal: ₹{{ number_format($price['subtotal'], 2) }}</p>

                    {{-- Displaying Discounts --}}
                    @if (
                        $price['discounts']['user_type'] > 0 ||
                        $price['discounts']['at_home_option'] > 0 ||
                        $price['discounts']['subtotal'] > 0 ||
                        $price['discounts']['attribute'] > 0
                    )
                        <p class="font-semibold mt-2">Discounts:</p>
                        <ul class="list-disc ml-6">
                            {{-- Company User Type Discount --}}
                            @if ($price['discounts']['user_type'] > 0)
                                <li>- 20% for user type company: -₹{{ number_format($price['discounts']['user_type'], 2) }}</li>
                            @endif

                            {{-- At Home Option Discount --}}
                            @if ($price['discounts']['at_home_option'] > 0)
                                <li>- 5% Off At Home: -₹{{ number_format($price['discounts']['at_home_option'], 2) }}</li>
                            @endif

                            {{-- Subtotal-based Discount (e.g., "10 Over 130") --}}
                            {{-- IMPORTANT: This assumes 'subtotal' discount is always the "10 Over 130" one for this display --}}
                            @if ($price['discounts']['subtotal'] > 0)
                                <li>- 10 Over 130: -₹{{ number_format($price['discounts']['subtotal'], 2) }}</li>
                            @endif

                            {{-- Other Attribute-based Discounts (if any, not specific like "At Home") --}}
                            @if ($price['discounts']['attribute'] > 0)
                                <li>- 5% off At home: -₹{{ number_format($price['discounts']['attribute'], 2) }}</li>
                            @endif
                        </ul>
                    @endif

                    <p class="font-bold mt-2">Total: ₹{{ number_format($price['final'], 2) }}</p>
                </div>
            @endif

        </div>
    @endif
</div>
</div>