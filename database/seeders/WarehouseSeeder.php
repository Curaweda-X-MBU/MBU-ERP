<?php

namespace Database\Seeders;

use App\Models\DataMaster\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Warehouse::truncate();

        Warehouse::insert([
            // Gudang kandang Singaparna 1
            [
                'name'        => 'Singaparna 1',
                'type'        => 2,
                'location_id' => 1,
                'kandang_id'  => 1,
                'created_by'  => 1,
            ],
            // Gudang kandang Singaparna 2
            [
                'name'        => 'Singaparna 2',
                'type'        => 2,
                'location_id' => 1,
                'kandang_id'  => 2,
                'created_by'  => 1,
            ],
            // Gudang kandang Singaparna 3
            [
                'name'        => 'Singaparna 3',
                'type'        => 2,
                'location_id' => 1,
                'kandang_id'  => 3,
                'created_by'  => 1,
            ],
            // Gudang Lokasi Singaparna
            [
                'name'        => 'Singaparna',
                'type'        => 1,
                'location_id' => 1,
                'kandang_id'  => null,
                'created_by'  => 1,
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
