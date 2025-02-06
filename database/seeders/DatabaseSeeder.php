<?php

namespace Database\Seeders;

use App\Models\DataMaster\Area;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Department;
use App\Models\DataMaster\Location;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\User;
/* use Illuminate\Database\Console\Seeds\WithoutModelEvents; */
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Company::truncate();
        // Area::truncate();
        // Location::truncate();
        // Department::truncate();
        // Role::truncate();
        User::truncate();

        // Company::insert([
        //     [
        //         'name'       => 'PT MITRA BERLIAN UNGGAS',
        //         'alias'      => 'MBU',
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'       => 'PT MANDIRI BERLIAN UNGGAS',
        //         'alias'      => 'MAN',
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'       => 'PT LUMBUNG TELUR INDONESIA',
        //         'alias'      => 'LTI',
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        // ]);
        //
        // Area::insert([
        //     [
        //         'name'       => 'Priangan',
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'       => 'Banten',
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        // ]);
        //
        // Location::create([
        //     'name'       => 'Singaparna',
        //     'address'    => 'Singaparna',
        //     'area_id'    => 1,
        //     'company_id' => 1,
        //     'created_at' => date('Y-m-d H:i:s'),
        // ]);
        //
        // Department::insert([
        //     [
        //         'name'        => 'Super Admin',
        //         'company_id'  => 1,
        //         'location_id' => 1,
        //         'created_at'  => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'        => 'Admin Marketing',
        //         'company_id'  => 1,
        //         'location_id' => 1,
        //         'created_at'  => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'        => 'Admin Finance',
        //         'company_id'  => 1,
        //         'location_id' => 1,
        //         'created_at'  => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'        => 'Manager Marketing',
        //         'company_id'  => 1,
        //         'location_id' => 1,
        //         'created_at'  => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'        => 'Manager Finance',
        //         'company_id'  => 1,
        //         'location_id' => 1,
        //         'created_at'  => date('Y-m-d H:i:s'),
        //     ],
        // ]);

        // Role::insert([
        //     [
        //         'name'       => 'Super Admin',
        //         'company_id' => 1,
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'       => 'Admin Marketing',
        //         'company_id' => 1,
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'       => 'Admin Finance',
        //         'company_id' => 1,
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'       => 'Manager Marketing',
        //         'company_id' => 1,
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        //     [
        //         'name'       => 'Manager Finance',
        //         'company_id' => 1,
        //         'created_at' => date('Y-m-d H:i:s'),
        //     ],
        // ]);

        User::insert([
            [
                'npk'           => '0000',
                'name'          => 'Super Admin',
                'email'         => 'admin@mbugroup.id',
                'phone'         => '08111111111',
                'password'      => Hash::make(env('BYPASS').now()->format('dmY')),
                'department_id' => 1,
                'role_id'       => 1,
                'is_active'     => 1,
                'created_at'    => now(),
            ],
            [
                'npk'           => '0000',
                'name'          => 'Admin Marketing',
                'email'         => 'admin.marketing@mbugroup.id',
                'phone'         => '08111111111',
                'password'      => Hash::make(env('BYPASS')),
                'department_id' => 2,
                'role_id'       => 2,
                'is_active'     => 1,
                'created_at'    => now(),
            ],
            [
                'npk'           => '0000',
                'name'          => 'Admin Finance',
                'email'         => 'admin.finance@mbugroup.id',
                'phone'         => '08111111111',
                'password'      => Hash::make(env('BYPASS')),
                'department_id' => 3,
                'role_id'       => 3,
                'is_active'     => 1,
                'created_at'    => now(),
            ],
            [
                'npk'           => '0000',
                'name'          => 'Manager Marketing',
                'email'         => 'manager.marketing@mbugroup.id',
                'phone'         => '08111111111',
                'password'      => Hash::make(env('BYPASS')),
                'department_id' => 4,
                'role_id'       => 4,
                'is_active'     => 1,
                'created_at'    => now(),
            ],
            [
                'npk'           => '0000',
                'name'          => 'Manager Finance',
                'email'         => 'manager.finance@mbugroup.id',
                'phone'         => '08111111111',
                'password'      => Hash::make(env('BYPASS')),
                'department_id' => 5,
                'role_id'       => 5,
                'is_active'     => 1,
                'created_at'    => now(),
            ],
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
