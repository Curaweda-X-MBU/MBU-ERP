<?php

namespace Database\Seeders;

use App\Models\DataMaster\Kandang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KandangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Kandang::truncate();

        Kandang::insert([
            [
                'name'        => 'Singaparna 1',
                'capacity'    => 50_000,
                'type'        => 1,
                'pic'         => 1,
                'location_id' => 1,
                'company_id'  => 1,
                'created_by'  => 1,
            ],
            [
                'name'        => 'Singaparna 2',
                'capacity'    => 50_000,
                'type'        => 1,
                'pic'         => 1,
                'location_id' => 1,
                'company_id'  => 2,
                'created_by'  => 1,
            ],
            [
                'name'        => 'Singaparna 3',
                'capacity'    => 50_000,
                'type'        => 1,
                'pic'         => 1,
                'location_id' => 1,
                'company_id'  => 3,
                'created_by'  => 1,
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
