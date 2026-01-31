# Laravel Turkey Geo Database

[![Latest Version](https://img.shields.io/packagist/v/hakanispirli/laravel-turkey-geo-database.svg)](https://packagist.org/packages/hakanispirli/laravel-turkey-geo-database)
[![Total Downloads](https://img.shields.io/packagist/dt/hakanispirli/laravel-turkey-geo-database.svg)](https://packagist.org/packages/hakanispirli/laravel-turkey-geo-database)
[![License](https://img.shields.io/packagist/l/hakanispirli/laravel-turkey-geo-database.svg)](https://packagist.org/packages/hakanispirli/laravel-turkey-geo-database)

Complete Turkish geographic data package for Laravel applications. Get all Turkish cities, districts, and neighborhoods with postal codes in your database with just a few commands.

## Installation

Install the package via Composer:

```bash
composer require hakanispirli/laravel-turkey-geo-database
```

## Quick Setup (3 Steps)

### Step 1: Publish Data Files

```bash
php artisan vendor:publish --tag=turkey-geo-data
```

### Step 2: Run Migrations

```bash
php artisan migrate
```

This creates three tables: `cities`, `districts`, and `neighborhoods`.

### Step 3: Seed the Database

```bash
php artisan db:seed --class="Webmarka\TurkeyGeo\Database\Seeders\TurkeyGeoSeeder"
```

**That's it!** You now have complete Turkish geographic data in your database.

> **⏱️ Seeding Time**: Approximately 30-60 seconds to insert all data with progress tracking.

## Basic Usage

### Get All Cities

```php
use Webmarka\TurkeyGeo\Models\City;

$cities = City::all();
```

### Get Districts for a City

```php
$ankara = City::where('name', 'ANKARA')->first();
$districts = $ankara->districts;
```

### Get Neighborhoods for a District

```php
use Webmarka\TurkeyGeo\Models\District;

$district = District::find(1);
$neighborhoods = $district->neighborhoods;
```

### Search by Postal Code

```php
use Webmarka\TurkeyGeo\Models\Neighborhood;

$neighborhoods = Neighborhood::where('postal_code', '06100')->get();
```

## Common Use Cases

### 1. City Dropdown for Forms

```php
// Controller
public function create()
{
    $cities = City::orderBy('name')->get();
    return view('address.create', compact('cities'));
}
```

```html
<!-- Blade View -->
<select name="city_id">
    <option value="">Select City</option>
    @foreach($cities as $city)
        <option value="{{ $city->id }}">{{ $city->name }}</option>
    @endforeach
</select>
```

### 2. Dynamic District Loading (AJAX)

```php
// Route
Route::get('/api/districts/{cityId}', function ($cityId) {
    return District::where('city_id', $cityId)
        ->orderBy('name')
        ->get(['id', 'name']);
});
```

```javascript
// JavaScript
$('#city_id').change(function() {
    let cityId = $(this).val();
    $.get(`/api/districts/${cityId}`, function(districts) {
        $('#district_id').html('<option value="">Select District</option>');
        districts.forEach(district => {
            $('#district_id').append(`<option value="${district.id}">${district.name}</option>`);
        });
    });
});
```

### 3. Get Full Address Details

```php
$neighborhood = Neighborhood::with('district.city')->find($id);

echo $neighborhood->name; // Neighborhood name
echo $neighborhood->district->name; // District name
echo $neighborhood->district->city->name; // City name
echo $neighborhood->postal_code; // Postal code
```

## Database Structure

**cities**
- `id` - City ID (1-81)
- `name` - City name

**districts**
- `id` - District ID
- `city_id` - Belongs to city
- `name` - District name

**neighborhoods**
- `id` - Neighborhood ID
- `district_id` - Belongs to district
- `name` - Neighborhood name
- `area` - Area/region information
- `postal_code` - PTT postal code

## Customization

### Publish Migrations (Optional)

If you need to customize the database tables (add columns, change types, etc.):

```bash
php artisan vendor:publish --tag=turkey-geo-migrations
```

Then modify the published migration files in your `database/migrations` directory before running `php artisan migrate`.

### Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=turkey-geo-config
```

Edit `config/turkey-geo.php` to customize:
- Table names
- Seeding batch size
- Progress display options

## Advanced Features

### Eager Loading Relationships

```php
// Load city with all its districts
$city = City::with('districts')->find(7);

// Load district with neighborhoods
$district = District::with('neighborhoods')->find(1);
```

### Validation Example

```php
use Illuminate\Validation\Rule;

public function rules()
{
    return [
        'city_id' => 'required|exists:cities,id',
        'district_id' => [
            'required',
            Rule::exists('districts', 'id')->where('city_id', $this->city_id),
        ],
        'neighborhood_id' => [
            'required',
            Rule::exists('neighborhoods', 'id')->where('district_id', $this->district_id),
        ],
    ];
}
```

### Caching for Performance

```php
use Illuminate\Support\Facades\Cache;

$cities = Cache::remember('turkish-cities', 3600, function () {
    return City::orderBy('name')->get();
});
```

## Troubleshooting

### Seeder Class Not Found

Run composer autoload dump:

```bash
composer dump-autoload
```

### Data Files Not Found Error

Make sure you published the data files:

```bash
php artisan vendor:publish --tag=turkey-geo-data
```

Verify files exist in `database/data/turkey-geo/` directory.

### Memory Issues During Seeding

Reduce batch size in config file:

```php
// config/turkey-geo.php
'seeding' => [
    'batch_size' => 500, // Default is 1000
],
```

## Requirements

- PHP ^8.2
- Laravel ^11.0 or ^12.0

## What's Included

- **81 Turkish Cities** (İller)
- **900+ Districts** (İlçeler)
- **50,000+ Neighborhoods** (Mahalleler)
- **PTT Postal Codes** for all neighborhoods
- **Optimized Performance** with indexed database columns
- **Eloquent Models** with pre-configured relationships
- **Progress Tracking** during data seeding

## Contributing

Contributions are welcome! Please submit a Pull Request.

## Security

If you discover any security issues, please email destek@webmarka.com.

## Credits

- [Hakan İspirli](https://github.com/hakanispirli)
- [Webmarka](https://webmarka.com)
- PTT (Turkish Post) for official postal code data

## License

The MIT License (MIT). See [License File](LICENSE) for more information.

---

**Made with ❤️ by [Webmarka](https://webmarka.com)**
