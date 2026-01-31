<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | Customize the table names for the geographic data.
    |
    */

    'tables' => [
        'cities' => 'cities',
        'districts' => 'districts',
        'neighborhoods' => 'neighborhoods',
    ],

    /*
    |--------------------------------------------------------------------------
    | Seeding Options
    |--------------------------------------------------------------------------
    |
    | Configure the seeding behavior for initial data loading.
    |
    */

    'seeding' => [
        // Batch size for inserting neighborhoods (performance optimization)
        'batch_size' => 1000,
        
        // Enable progress output during seeding
        'show_progress' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Path
    |--------------------------------------------------------------------------
    |
    | Path to the published JSON data files. This is automatically set
    | when you publish the data files using artisan command.
    |
    */

    'data_path' => database_path('data/turkey-geo'),

];
