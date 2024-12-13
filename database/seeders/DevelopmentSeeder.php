<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DatabaseSeeder::class,
            UomSeeder::class,
            ProductSeeder::class,
            FcrSeeder::class,
            KandangSeeder::class,
            WarehouseSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
