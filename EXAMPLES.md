# Laravel Turkey Geo Database - Example Application

This directory contains example code demonstrating how to use the Laravel Turkey Geo Database package in your application.

## Quick Examples

### 1. City Selection Dropdown

```php
// Controller
public function create()
{
    $cities = \Webmarka\TurkeyGeo\Models\City::orderBy('name')->get();
    return view('address.create', compact('cities'));
}

// View (Blade)
<select name="city_id" id="city_id">
    <option value="">Select City</option>
    @foreach($cities as $city)
        <option value="{{ $city->id }}">{{ $city->name }}</option>
    @endforeach
</select>
```

### 2. AJAX District Loading

```php
// Route
Route::get('/api/districts/{cityId}', function ($cityId) {
    return \Webmarka\TurkeyGeo\Models\District::where('city_id', $cityId)
        ->orderBy('name')
        ->get(['id', 'name']);
});

// JavaScript
$('#city_id').change(function() {
    let cityId = $(this).val();
    $.get(`/api/districts/${cityId}`, function(districts) {
        $('#district_id').empty().append('<option value="">Select District</option>');
        districts.forEach(district => {
            $('#district_id').append(`<option value="${district.id}">${district.name}</option>`);
        });
    });
});
```

### 3. Address Form Component

```php
// Livewire Component
class AddressForm extends Component
{
    public $city_id;
    public $district_id;
    public $neighborhood_id;
    
    public $cities;
    public $districts = [];
    public $neighborhoods = [];
    
    public function mount()
    {
        $this->cities = City::orderBy('name')->get();
    }
    
    public function updatedCityId($value)
    {
        $this->districts = District::where('city_id', $value)
            ->orderBy('name')
            ->get();
        $this->district_id = null;
        $this->neighborhoods = [];
        $this->neighborhood_id = null;
    }
    
    public function updatedDistrictId($value)
    {
        $this->neighborhoods = Neighborhood::where('district_id', $value)
            ->orderBy('name')
            ->get();
        $this->neighborhood_id = null;
    }
}
```

### 4. Search Functionality

```php
// Search neighborhoods by name
$results = Neighborhood::where('name', 'LIKE', '%' . $searchTerm . '%')
    ->with('district.city')
    ->limit(10)
    ->get();

// Search by postal code
$neighborhoods = Neighborhood::where('postal_code', $postalCode)
    ->with('district.city')
    ->get();
```

### 5. Full Address Display

```php
// Model method (add to your User model)
public function fullAddress()
{
    $neighborhood = Neighborhood::find($this->neighborhood_id);
    
    return [
        'street' => $this->street_address,
        'neighborhood' => $neighborhood->name,
        'district' => $neighborhood->district->name,
        'city' => $neighborhood->district->city->name,
        'postal_code' => $neighborhood->postal_code,
    ];
}

// Usage
$address = $user->fullAddress();
echo "{$address['street']}, {$address['neighborhood']}, {$address['district']}/{$address['city']} - {$address['postal_code']}";
```

### 6. Validation Rules

```php
// Form Request
public function rules()
{
    return [
        'city_id' => 'required|exists:cities,id',
        'district_id' => [
            'required',
            'exists:districts,id',
            Rule::exists('districts', 'id')->where('city_id', $this->city_id),
        ],
        'neighborhood_id' => [
            'required',
            'exists:neighborhoods,id',
            Rule::exists('neighborhoods', 'id')->where('district_id', $this->district_id),
        ],
    ];
}
```

### 7. Seeding User Addresses

```php
// Factory
use Webmarka\TurkeyGeo\Models\{City, District, Neighborhood};

public function definition()
{
    $city = City::inRandomOrder()->first();
    $district = $city->districts()->inRandomOrder()->first();
    $neighborhood = $district->neighborhoods()->inRandomOrder()->first();
    
    return [
        'city_id' => $city->id,
        'district_id' => $district->id,
        'neighborhood_id' => $neighborhood->id,
        'postal_code' => $neighborhood->postal_code,
        'street_address' => fake()->streetAddress(),
    ];
}
```

## Best Practices

1. **Cache Static Data**: Cities rarely change, cache them
2. **Eager Loading**: Always use `with()` to prevent N+1 queries
3. **Validation**: Validate that district belongs to selected city
4. **AJAX**: Load districts/neighborhoods dynamically for better UX
5. **Indexes**: The package includes proper indexes, use them in queries

## API Resource Example

```php
class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'city' => [
                'id' => $this->city_id,
                'name' => $this->city->name,
            ],
            'district' => [
                'id' => $this->district_id,
                'name' => $this->district->name,
            ],
            'neighborhood' => [
                'id' => $this->neighborhood_id,
                'name' => $this->neighborhood->name,
                'postal_code' => $this->neighborhood->postal_code,
            ],
            'street_address' => $this->street_address,
        ];
    }
}
```
