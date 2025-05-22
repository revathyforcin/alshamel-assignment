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
<code>git clone https://github.com/revathyforcin/alshamel-assignment.git
cd alshamel-assignment</code>
You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
