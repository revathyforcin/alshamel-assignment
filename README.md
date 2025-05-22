Price calculator configurator

## About Price calculator configurator
<p>This is a Laravel 12 Livewire application that calculates the final price of a product based on selected attributes and dynamic discount rules. The app demonstrates logic-based price computation with real-time UI updates using Livewire.</p>


## Features
* Select a product from a seeded list
* Choose configuration options (attributes) like delivery method, speed, size, etc.
* Switch user type (normal or company)
* Real-time price updates and breakdown:
    * Base product price
    * Attribute-based additions
    * Applied discounts


## Discount Types Supported
1. Attribute-based: e.g., 5% off for "At Home" delivery
2. Subtotal-based: e.g., 10 KD off if subtotal > 130
3. User-type-based: e.g., 20% off for company users


## Example Scenario
* Product: Toy Car (Base price: 100 KD)
* Attributes:
    - Delivery Method: At Home (+20 KD), In Lab (+0 KD)
    - Speed: Same Day (+30 KD), Next Day (+10 KD)
* Discounts:
    - 5% off if Delivery = At Home
    - 10 KD off if subtotal > 130
    - 20% off for user type = company

## Tech Stack
    - Laravel 12
    - Livewire
    - Tailwind CSS


## Setup Instructions
1. Clone the repository:
    <pre>git clone https://github.com/revathyforcin/alshamel-assignment.git
cd alshamel-assignment</pre>
2. Install dependencies:
    <pre>composer install</pre>
3. Copy the .env and generate app key:
    <pre>cp .env.example .env
php artisan key:generate</pre>
4. Configure your database in the .env file and run migrations with seeders:
    <pre>php artisan migrate --seed</pre>
5. Start the server:
    <pre>php artisan serve</pre>

