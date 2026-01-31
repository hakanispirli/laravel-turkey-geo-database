# Webmarka\TurkeyGeo

## Overview
This namespace contains all package components for the Laravel Turkey Geo Database package.

## Directory Structure

```
src/
├── config/
│   └── turkey-geo.php          # Package configuration
├── Database/
│   ├── Migrations/             # Database migration files
│   │   ├── 2024_01_01_000001_create_cities_table.php
│   │   ├── 2024_01_01_000002_create_districts_table.php
│   │   └── 2024_01_01_000003_create_neighborhoods_table.php
│   └── Seeders/
│       └── TurkeyGeoSeeder.php # Main seeder class
├── Models/
│   ├── City.php                # City model
│   ├── District.php            # District model
│   └── Neighborhood.php        # Neighborhood model
└── TurkeyGeoServiceProvider.php # Service provider
```

## Models

### City
- Represents Turkish cities (İller)
- Has many districts

### District
- Represents Turkish districts (İlçeler)
- Belongs to a city
- Has many neighborhoods

### Neighborhood
- Represents Turkish neighborhoods (Mahalleler)
- Belongs to a district
- Contains postal code and area information

## Service Provider

The `TurkeyGeoServiceProvider` handles:
- Package configuration registration
- Migration publishing
- Data file publishing
- Auto-discovery for Laravel

## Configuration

All configurable options are in `config/turkey-geo.php`:
- Table names
- Batch sizes for seeding
- Progress display options
- Data file paths
