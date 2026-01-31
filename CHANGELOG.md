# Changelog

All notable changes to `laravel-turkey-geo-database` will be documented in this file.

## [1.0.0] - 2026-01-31

### Added
- Initial release of Laravel Turkey Geo Database package
- Database models for Cities, Districts, and Neighborhoods
- Optimized migrations with proper indexing
- Performance-optimized seeder for 81 cities, 900+ districts, and 50,000+ neighborhoods
- Support for postal codes and area information
- Configurable table names and batch sizes
- Progress tracking during seeding
- Laravel 11 and Laravel 12 support
- Publishable migrations, config, and data files
- Comprehensive documentation

### Features
- 81 Turkish cities (İller)
- 900+ Turkish districts (İlçeler)
- 50,000+ Turkish neighborhoods (Mahalleler)
- PTT postal code database
- Eloquent model relationships
- Auto-discovery service provider
- Memory-efficient seeding with chunked inserts
- Transaction-wrapped data insertion
