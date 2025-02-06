<?php

namespace Database\Seeders;

use App\Models\Inventory\ProductWarehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        ProductWarehouse::truncate();

        ProductWarehouse::insert([
            [
                'product_id'   => 1, // Telur Sehat
                'warehouse_id' => 3,
                'quantity'     => 25_000,
            ],
            [
                'product_id'   => 2, // Telur Retak
                'warehouse_id' => 3,
                'quantity'     => 5_000,
            ],
            [
                'product_id'   => 4, // Parent Stock Sehat
                'warehouse_id' => 2,
                'quantity'     => 25_000,
            ],
            [
                'product_id'   => 5, // Parent Stock Culling
                'warehouse_id' => 2,
                'quantity'     => 800,
            ],
            [
                'product_id'   => 7, // Final Stock Baik
                'warehouse_id' => 1,
                'quantity'     => 25_000,
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
