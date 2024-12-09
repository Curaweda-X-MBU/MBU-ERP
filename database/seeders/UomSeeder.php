<?php

namespace Database\Seeders;

use App\Models\DataMaster\Uom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Uom::truncate();

        Uom::insert([
            [
                'name' => 'Butir',
                'created_by' => 1,
            ],
            [
                'name' => 'Ekor',
                'created_by' => 1,
            ],
            [
                'name' => 'Kg',
                'created_by' => 1,
            ],
            [
                'name' => '%',
                'created_by' => 1,
            ],
            [
                'name' => 'Ratio',
                'created_by' => 1,
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
