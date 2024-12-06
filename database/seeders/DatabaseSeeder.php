<?php

namespace Database\Seeders;

use App\Models\DataMaster\Area;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Department;
use App\Models\DataMaster\Location;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\User;
/* use Illuminate\Database\Console\Seeds\WithoutModelEvents; */
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Company::truncate();
        Area::truncate();
        Location::truncate();
        Department::truncate();
        Role::truncate();
        User::truncate();

        Company::insert([
            [
                'name' => 'PT MITRA BERLIAN UNGGAS',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'PT MANDIRI BERLIAN UNGGAS',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'PT LUMBUNG TELUR INDONESIA',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);

        Area::insert([
            [
                'name' => 'Priangan',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Banten',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);

        Location::create([
            'name' => 'Singaparna',
            'address' => 'Singaparna',
            'area_id' => 1,
            'company_id' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        Department::create([
            'name' => 'Super Admin',
            'company_id' => 1,
            'location_id' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Role::create([
            'name' => 'Super Admin',
            'company_id' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        User::create([
            'npk' => '0000',
            'name' => 'Super Admin',
            'email' => 'admin@mbugroup.id',
            'phone' => '08111111111',
            'password' => Hash::make(env('BYPASS') . now()->format('dmY')),
            'department_id' => 1,
            'role_id' => 1,
            'is_active' => 1,
            'created_at' => now()
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
