<?php

namespace Database\Seeders;

use App\Models\DataMaster\Fcr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FcrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Fcr::truncate();

        Fcr::insert([
            [
                'name' => 'FCR TLR - Telur - Sehat',
                'value' => 90,
                'product_id' => 1,
                'uom_id' => 1,
                'created_by' => 1,
            ],
            [
                'name' => 'FCR TLR - Telur - Retak',
                'value' => 20,
                'product_id' => 2,
                'uom_id' => 1,
                'created_by' => 1,
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
