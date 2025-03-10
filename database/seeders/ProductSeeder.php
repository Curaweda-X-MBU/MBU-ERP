<?php

namespace Database\Seeders;

use App\Models\DataMaster\Product;
use App\Models\DataMaster\ProductCategory;
use App\Models\DataMaster\ProductSubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Product::truncate();
        ProductCategory::truncate();
        ProductSubCategory::truncate();

        ProductCategory::insert([
            [
                'name'          => 'Telur',          // id 1
                'category_code' => 'TLR',
                'created_by'    => 1,
            ],
            [
                'name'          => 'Parent Stock',   // id 2
                'category_code' => 'PRS',
                'created_by'    => 1,
            ],
            [
                'name'          => 'Final Stock',    // id 3
                'category_code' => 'FLS',
                'created_by'    => 1,
            ],
            [
                'name'          => 'Broiler',        // id 4
                'category_code' => 'BRO',
                'created_by'    => 1,
            ],
        ]);

        ProductSubCategory::insert([
            // Telur
            [
                'name'                => 'Sehat',
                'product_category_id' => 1,
                'created_by'          => 1,
            ],
            [
                'name'                => 'Retak',
                'product_category_id' => 1,
                'created_by'          => 1,
            ],
            [
                'name'                => 'Pecah',
                'product_category_id' => 1,
                'created_by'          => 1,
            ],
            // Parent Stock
            [
                'name'                => 'Sehat',
                'product_category_id' => 2,
                'created_by'          => 2,
            ],
            [
                'name'                => 'Culling',
                'product_category_id' => 2,
                'created_by'          => 2,
            ],
            [
                'name'                => 'Afkir',
                'product_category_id' => 2,
                'created_by'          => 2,
            ],
            [
                'name'                => 'Mati',
                'product_category_id' => 2,
                'created_by'          => 2,
            ],
            // Final Stock
            [
                'name'                => 'Baik',
                'product_category_id' => 3,
                'created_by'          => 3,
            ],
        ]);

        Product::insert([
            [
                // Product Category Code - Category Name - Sub Category Name
                'name'                    => 'TLR - Telur - Sehat',
                'brand'                   => 'Brand 3',
                'uom_id'                  => 1,
                'sku'                     => strval(random_int(1000000000, 9999999999)),
                'company_id'              => 3,
                'product_category_id'     => 1,
                'product_sub_category_id' => 1,
                'product_price'           => 1000,
                'selling_price'           => 1500,
                'tax'                     => 15,
                'expiry_period'           => 30,
                'can_be_sold'             => 1,
                'can_be_purchased'        => 1,
                'is_active'               => 1,
                'created_by'              => 1,
            ],
            [
                // Product Category Code - Category Name - Sub Category Name
                'name'                    => 'TLR - Telur - Retak',
                'brand'                   => 'Brand 3',
                'uom_id'                  => 1,
                'sku'                     => strval(random_int(1000000000, 9999999999)),
                'company_id'              => 3,
                'product_category_id'     => 1,
                'product_sub_category_id' => 2,
                'product_price'           => 800,
                'selling_price'           => 1000,
                'tax'                     => 15,
                'expiry_period'           => 30,
                'can_be_sold'             => 1,
                'can_be_purchased'        => 1,
                'is_active'               => 1,
                'created_by'              => 1,
            ],
            [
                // Product Category Code - Category Name - Sub Category Name
                'name'                    => 'TLR - Telur - Pecah',
                'brand'                   => 'Brand 3',
                'uom_id'                  => 1,
                'sku'                     => strval(random_int(1000000000, 9999999999)),
                'company_id'              => 3,
                'product_category_id'     => 1,
                'product_sub_category_id' => 3,
                'product_price'           => 200,
                'selling_price'           => 500,
                'tax'                     => 15,
                'expiry_period'           => 30,
                'can_be_sold'             => 0,
                'can_be_purchased'        => 0,
                'is_active'               => 1,
                'created_by'              => 1,
            ],
            [
                // Product Category Code - Category Name - Sub Category Name
                'name'                    => 'PRS - Parent Stock - Sehat',
                'brand'                   => 'Brand 1',
                'uom_id'                  => 2,
                'sku'                     => strval(random_int(1000000000, 9999999999)),
                'company_id'              => 1,
                'product_category_id'     => 2,
                'product_sub_category_id' => 1,
                'product_price'           => 20000,
                'selling_price'           => 30000,
                'tax'                     => 15,
                'expiry_period'           => null,
                'can_be_sold'             => 1,
                'can_be_purchased'        => 1,
                'is_active'               => 1,
                'created_by'              => 1,
            ],
            [
                // Product Category Code - Category Name - Sub Category Name
                'name'                    => 'PRS - Parent Stock - Culling',
                'brand'                   => 'Brand 1',
                'uom_id'                  => 2,
                'sku'                     => strval(random_int(1000000000, 9999999999)),
                'company_id'              => 1,
                'product_category_id'     => 2,
                'product_sub_category_id' => 2,
                'product_price'           => 10000,
                'selling_price'           => 15000,
                'tax'                     => 15,
                'expiry_period'           => 15,
                'can_be_sold'             => 1,
                'can_be_purchased'        => 1,
                'is_active'               => 1,
                'created_by'              => 1,
            ],
            [
                // Product Category Code - Category Name - Sub Category Name
                'name'                    => 'PRS - Parent Stock - Afkir',
                'brand'                   => 'Brand 2',
                'uom_id'                  => 2,
                'sku'                     => strval(random_int(1000000000, 9999999999)),
                'company_id'              => 3,
                'product_category_id'     => 2,
                'product_sub_category_id' => 3,
                'product_price'           => 10000,
                'selling_price'           => 15000,
                'tax'                     => 15,
                'expiry_period'           => 10,
                'can_be_sold'             => 0,
                'can_be_purchased'        => 0,
                'is_active'               => 1,
                'created_by'              => 1,
            ],
            [
                // Product Category Code - Category Name - Sub Category Name
                'name'                    => 'FLS - Final Stock - Baik',
                'brand'                   => 'Brand 4',
                'uom_id'                  => 3,
                'sku'                     => strval(random_int(1000000000, 9999999999)),
                'company_id'              => 3,
                'product_category_id'     => 2,
                'product_sub_category_id' => 3,
                'product_price'           => 30000,
                'selling_price'           => 35000,
                'tax'                     => 15,
                'expiry_period'           => 15,
                'can_be_sold'             => 1,
                'can_be_purchased'        => 1,
                'is_active'               => 1,
                'created_by'              => 1,
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
