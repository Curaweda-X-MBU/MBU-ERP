<?php

namespace Database\Seeders;

use App\Models\DataMaster\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Customer::truncate();

        Customer::insert([
            [
                'name'      => 'Agus Sukirman',
                'assign_to' => 1,
                'type'      => 1,
                'address'   => 'Bandung',
            ],
            [
                'name'      => 'Agus Sukija',
                'assign_to' => 1,
                'type'      => 1,
                'address'   => 'Bandung',
            ],
            [
                'name'      => 'Asep Hermawan',
                'assign_to' => 1,
                'type'      => 1,
                'address'   => 'Bandung',
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
