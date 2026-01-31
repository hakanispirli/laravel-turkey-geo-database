<?php

namespace Webmarka\TurkeyGeo\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Webmarka\TurkeyGeo\Models\City;
use Webmarka\TurkeyGeo\Models\District;
use Webmarka\TurkeyGeo\Models\Neighborhood;

class TurkeyGeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataPath = config('turkey-geo.data_path');
        $batchSize = config('turkey-geo.seeding.batch_size', 1000);
        $showProgress = config('turkey-geo.seeding.show_progress', true);

        if ($showProgress) {
            $this->command->info('🇹🇷 Starting Turkey Geographic Data Seeding...');
            $this->command->newLine();
        }

        // Seed Cities
        $this->seedCities($dataPath, $showProgress);

        // Seed Districts
        $this->seedDistricts($dataPath, $showProgress);

        // Seed Neighborhoods
        $this->seedNeighborhoods($dataPath, $batchSize, $showProgress);

        if ($showProgress) {
            $this->command->newLine();
            $this->command->info('✅ Turkey Geographic Data seeding completed successfully!');
        }
    }

    /**
     * Seed cities from JSON file.
     */
    protected function seedCities(string $dataPath, bool $showProgress): void
    {
        if ($showProgress) {
            $this->command->task('Seeding cities', function () use ($dataPath) {
                $jsonPath = $dataPath . '/cities.json';

                if (!File::exists($jsonPath)) {
                    throw new \RuntimeException("Cities JSON file not found at: {$jsonPath}");
                }

                $data = json_decode(File::get($jsonPath), true);
                $cities = $data['cities'] ?? [];

                if (empty($cities)) {
                    throw new \RuntimeException('No cities data found in JSON file');
                }

                // Disable timestamps temporarily for performance
                City::unguarded(function () use ($cities) {
                    DB::transaction(function () use ($cities) {
                        foreach ($cities as $city) {
                            DB::table(config('turkey-geo.tables.cities', 'cities'))->insert([
                                'id' => $city['id'],
                                'name' => $city['name'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    });
                });

                return true;
            });
        } else {
            $jsonPath = $dataPath . '/cities.json';
            $data = json_decode(File::get($jsonPath), true);
            $cities = $data['cities'] ?? [];

            City::unguarded(function () use ($cities) {
                DB::transaction(function () use ($cities) {
                    foreach ($cities as $city) {
                        DB::table(config('turkey-geo.tables.cities', 'cities'))->insert([
                            'id' => $city['id'],
                            'name' => $city['name'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
            });
        }
    }

    /**
     * Seed districts from JSON file.
     */
    protected function seedDistricts(string $dataPath, bool $showProgress): void
    {
        if ($showProgress) {
            $this->command->task('Seeding districts', function () use ($dataPath) {
                $jsonPath = $dataPath . '/districts.json';

                if (!File::exists($jsonPath)) {
                    throw new \RuntimeException("Districts JSON file not found at: {$jsonPath}");
                }

                $data = json_decode(File::get($jsonPath), true);
                $districts = $data['districts'] ?? [];

                if (empty($districts)) {
                    throw new \RuntimeException('No districts data found in JSON file');
                }

                // Disable timestamps temporarily for performance
                District::unguarded(function () use ($districts) {
                    DB::transaction(function () use ($districts) {
                        // Insert in chunks for better performance
                        foreach (array_chunk($districts, 500) as $chunk) {
                            $insertData = array_map(function ($district) {
                                return [
                                    'id' => $district['id'],
                                    'city_id' => $district['city_id'],
                                    'name' => $district['name'],
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }, $chunk);

                            DB::table(config('turkey-geo.tables.districts', 'districts'))->insert($insertData);
                        }
                    });
                });

                return true;
            });
        } else {
            $jsonPath = $dataPath . '/districts.json';
            $data = json_decode(File::get($jsonPath), true);
            $districts = $data['districts'] ?? [];

            District::unguarded(function () use ($districts) {
                DB::transaction(function () use ($districts) {
                    foreach (array_chunk($districts, 500) as $chunk) {
                        $insertData = array_map(function ($district) {
                            return [
                                'id' => $district['id'],
                                'city_id' => $district['city_id'],
                                'name' => $district['name'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }, $chunk);

                        DB::table(config('turkey-geo.tables.districts', 'districts'))->insert($insertData);
                    }
                });
            });
        }
    }

    /**
     * Seed neighborhoods from 81 JSON files.
     */
    protected function seedNeighborhoods(string $dataPath, int $batchSize, bool $showProgress): void
    {
        $neighborhoodsPath = $dataPath . '/neighborhoods';

        if (!File::isDirectory($neighborhoodsPath)) {
            throw new \RuntimeException("Neighborhoods directory not found at: {$neighborhoodsPath}");
        }

        // Get all neighborhood JSON files (01.json to 81.json)
        $files = File::files($neighborhoodsPath);
        $totalFiles = count($files);

        if ($showProgress) {
            $this->command->info("Processing {$totalFiles} neighborhood files...");
            $progressBar = $this->command->getOutput()->createProgressBar($totalFiles);
            $progressBar->start();
        }

        $totalInserted = 0;

        foreach ($files as $file) {
            $data = json_decode(File::get($file->getPathname()), true);
            $neighborhoods = $data['neighborhoods'] ?? [];

            if (!empty($neighborhoods)) {
                Neighborhood::unguarded(function () use ($neighborhoods, $batchSize, &$totalInserted) {
                    DB::transaction(function () use ($neighborhoods, $batchSize, &$totalInserted) {
                        foreach (array_chunk($neighborhoods, $batchSize) as $chunk) {
                            $insertData = array_map(function ($neighborhood) {
                                return [
                                    'id' => $neighborhood['id'],
                                    'district_id' => $neighborhood['district_id'],
                                    'name' => $neighborhood['name'],
                                    'area' => $neighborhood['area'] ?? null,
                                    'postal_code' => $neighborhood['postal_code'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }, $chunk);

                            DB::table(config('turkey-geo.tables.neighborhoods', 'neighborhoods'))->insert($insertData);
                            $totalInserted += count($chunk);
                        }
                    });
                });
            }

            if ($showProgress) {
                $progressBar->advance();
            }

            // Free memory
            unset($data, $neighborhoods);
        }

        if ($showProgress) {
            $progressBar->finish();
            $this->command->newLine();
            $this->command->info("✓ Inserted {$totalInserted} neighborhoods");
        }
    }
}
