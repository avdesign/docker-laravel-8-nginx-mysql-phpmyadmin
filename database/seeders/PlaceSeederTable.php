<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Place;

class PlaceSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Place::create([
            'name' => 'Berlin',
            'visited'  => 1,
            'lat' => 52.52,
            'lng' => 13.405
        ]);

        Place::create([
            'name' => 'Budapest',
            'visited' => 1,
            'lat' => 47.4979,
            'lng' => 19.0402
        ]);

        Place::create([
            'name' => 'Cincinnati',
            'visited' => 0,
            'lat' => 39.1031,
            'lng' => -84.512
        ]);

        Place::create([
            'name' => 'Denver',
            'visited' => 0,
            'lat' => 39.7392,
            'lng' => -104.99
        ]);

        Place::create([
            'name' => 'Helsinki',
            'visited' => 1,
            'lat' => 60.1699,
            'lng' => 24.9384
        ]);

        Place::create([
            'name' => 'Lisbon',
            'visited' => 1,
            'lat' => 38.7223,
            'lng' => -9.13934
        ]);

        Place::create([
            'name' => 'Moscow',
            'visited' => 0,
            'lat' => 55.7558,
            'lng' => 37.6173
        ]);

        Place::create([
            'name' => 'Nairobi',
            'visited' => 0,
            'lat' => -1.29207,
            'lng' => 36.8219
        ]);

        Place::create([
            'name' => 'Oslo',
            'visited' => 1,
            'lat' => 59.9139,
            'lng' => 10.7522
        ]);

        Place::create([
            'name' => 'Rio',
            'visited' => 15,
            'lat' => -22.9068,
            'lng' => -43.1729
        ]);


        Place::create([
            'name' => 'Tokyo',
            'visited' => 3,
            'lat' => 35.6895,
            'lng' => 139.692
        ]);

    }
}
