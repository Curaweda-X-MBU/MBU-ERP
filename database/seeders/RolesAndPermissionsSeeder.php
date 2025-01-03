<?php

namespace Database\Seeders;

use App\Models\UserManagement\Permission;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'Super Admin', 'company_id' => 1, 'guard_name' => 'web'],
            ['name' => 'Admin Marketing', 'company_id' => 1, 'guard_name' => 'web'],
            ['name' => 'Admin Finance', 'company_id' => 1, 'guard_name' => 'web'],
            ['name' => 'Manager Marketing', 'company_id' => 1, 'guard_name' => 'web'],
            ['name' => 'Manager Finance', 'company_id' => 1, 'guard_name' => 'web'],
        ];
        Role::query()->update(['guard_name' => 'web']);

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], ['company_id' => $role['company_id']], ['guard_name' => $role['guard_name']]);
        }

        // $adminFarm = Role::create(['name' => 'Admin Farm']);
        // $managerArea = Role::create(['name' => 'Manager Area']);
        // $staffAudit = Role::create(['name' => 'Staff Audit']);
        // $auditHead = Role::create(['name' => 'Audit Head']);
        // $staffPoultryHealth = Role::create(['name' => 'Staff Poultry Health']);
        // $poultryHealthHead = Role::create(['name' => 'Poultry Health Head']);

        // Create permissions
        $permissions = [
            'dashboard.mbu.index',
            'dashboard.lti.index',
            'dashboard.manbu.index',
            'audit.index',
            'audit.add',
            'audit.edit',
            'audit.delete',
            'project.list.index',
            'project.list.add',
            'project.list.edit',
            'project.list.detail',
            'project.list.copy',
            'project.list.approve',
            'project.list.delete',
            'project.chick-in.index',
            'project.chick-in.add',
            'project.chick-in.edit',
            'project.chick-in.detail',
            'project.chick-in.approve',
            'project.chick-in.delete',
            'project.recording.index',
            'project.recording.add',
            'project.recording.detail',
            'project.perparation.index',
            'ph.performance.index',
            'ph.performance.detail',
            'ph.performance.download',
            'ph.performance.add',
            'ph.performance.edit',
            'ph.performance.delete',
            'ph.report-complaint.index',
            'ph.report-complaint.add',
            'ph.report-complaint.edit',
            'ph.report-complaint.detail',
            'ph.report-complaint.download',
            'ph.report-complaint.delete',
            'ph.report-complaint.upload-image',
            'ph.symptom.index',
            'ph.symptom.add',
            'ph.symptom.edit',
            'ph.symptom.delete',
            'marketing.list.index',
            'marketing.list.add',
            'marketing.list.edit',
            'marketing.list.detail',
            'marketing.list.delete',
            'marketing.list.realization',
            'marketing.list.approve',
            'marketing.list.payment.index',
            'marketing.list.payment.add',
            'marketing.list.payment.edit',
            'marketing.list.payment.detail',
            'marketing.list.payment.delete',
            'marketing.list.payment.approve',
            'marketing.return.index',
            'marketing.return.add',
            'marketing.return.edit',
            'marketing.return.detail',
            'marketing.return.delete',
            'marketing.return.approve',
            'marketing.return.payment.index',
            'marketing.return.payment.add',
            'marketing.return.payment.edit',
            'marketing.return.payment.detail',
            'marketing.return.payment.delete',
            'marketing.return.payment.approve',
            'expense.list.index',
            'expense.list.add',
            'expense.list.edit',
            'expense.list.detail',
            'expense.list.delete',
            'expense.list.approve',
            'expense.list.payment.index',
            'expense.list.payment.add',
            'expense.list.payment.edit',
            'expense.list.payment.detail',
            'expense.list.payment.delete',
            'expense.list.payment.approve',
            'expense.recap.index',
            'purchase.index',
            'purchase.add',
            'purchase.copy',
            'purchase.edit',
            'purchase.approve',
            'purchase.detail',
            'purchase.delete',
            'inventory.product.index',
            'inventory.product.edit',
            'inventory.product.detail',
            'inventory.adjustment.index',
            'inventory.adjustment.add',
            'data-master.product-category.index',
            'data-master.product-category.add',
            'data-master.product-category.edit',
            'data-master.product-category.delete',
            'data-master.product-sub-category.index',
            'data-master.product-sub-category.add',
            'data-master.product-sub-category.edit',
            'data-master.product-sub-category.delete',
            'data-master.product-component.index',
            'data-master.product-component.add',
            'data-master.product-component.edit',
            'data-master.product-component.delete',
            'data-master.product.index',
            'data-master.product.add',
            'data-master.product.edit',
            'data-master.product.delete',
            'data-master.bank.index',
            'data-master.bank.add',
            'data-master.bank.edit',
            'data-master.bank.delete',
            'data-master.kandang.index',
            'data-master.kandang.add',
            'data-master.kandang.edit',
            'data-master.kandang.delete',
            'data-master.area.index',
            'data-master.area.add',
            'data-master.area.edit',
            'data-master.area.delete',
            'data-master.location.index',
            'data-master.location.add',
            'data-master.location.edit',
            'data-master.location.delete',
            'data-master.company.index',
            'data-master.company.add',
            'data-master.company.edit',
            'data-master.company.delete',
            'data-master.department.index',
            'data-master.department.add',
            'data-master.department.edit',
            'data-master.department.delete',
            'data-master.supplier.index',
            'data-master.supplier.add',
            'data-master.supplier.edit',
            'data-master.supplier.delete',
            'data-master.customer.index',
            'data-master.customer.add',
            'data-master.customer.edit',
            'data-master.customer.delete',
            'data-master.uom.index',
            'data-master.uom.add',
            'data-master.uom.edit',
            'data-master.uom.delete',
            'data-master.warehouse.index',
            'data-master.warehouse.add',
            'data-master.warehouse.edit',
            'data-master.warehouse.delete',
            'data-master.fcr.index',
            'data-master.fcr.add',
            'data-master.fcr.edit',
            'data-master.fcr.delete',
            'data-master.nonstock.index',
            'data-master.nonstock.add',
            'data-master.nonstock.edit',
            'data-master.nonstock.delete',
            'user-management.user.index',
            'user-management.user.add',
            'user-management.user.edit',
            'user-management.user.delete',
            'user-management.role.index',
            'user-management.role.add',
            'user-management.role.edit',
            'user-management.role.delete',
            'user-management.permission.index',
            'user-management.permission.add',
            'user-management.permission.edit',
            'user-management.permission.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminMarketingPermissions = [
            'marketing.list.index',
            'marketing.list.add',
            'marketing.list.edit',
            'marketing.list.detail',
            'marketing.list.delete',
            'marketing.list.realization',
            'marketing.return.index',
            'marketing.return.add',
            'marketing.return.edit',
            'marketing.return.detail',
            'marketing.return.delete',
        ];

        $adminFinancePermissions = [
            'marketing.list.index',
            'marketing.list.detail',
            'marketing.list.payment.index',
            'marketing.list.payment.add',
            'marketing.list.payment.detail',
            'marketing.return.index',
            'marketing.return.detail',
            'marketing.return.payment.index',
            'marketing.return.payment.add',
            'marketing.return.payment.detail',
        ];

        $managerMarketingPermissions = [
            'marketing.list.index',
            'marketing.list.edit',
            'marketing.list.detail',
            'marketing.list.approve',
            'marketing.list.payment.index',
            'marketing.list.payment.edit',
            'marketing.list.payment.detail',
            'marketing.list.payment.approve',
            'marketing.return.index',
            'marketing.return.edit',
            'marketing.return.detail',
            'marketing.return.payment.index',
            'marketing.return.payment.edit',
            'marketing.return.payment.detail',
            'marketing.return.payment.approve',
        ];

        $managerFinancePermissions = [
            'marketing.list.index',
            'marketing.list.add',
            'marketing.list.payment.index',
            'marketing.list.payment.add',
            'marketing.list.payment.detail',
            'marketing.list.payment.approve',
            'marketing.return.index',
            'marketing.return.add',
            'marketing.return.detail',
            'marketing.return.payment.index',
            'marketing.return.payment.add',
            'marketing.return.payment.detail',
            'marketing.return.payment.approve',
        ];

        // Assign permissions to roles
        Role::find(1)->givePermissionTo($permissions);
        Role::find(2)->givePermissionTo($adminMarketingPermissions);
        Role::find(3)->givePermissionTo($adminFinancePermissions);
        Role::find(4)->givePermissionTo($managerMarketingPermissions);
        Role::find(5)->givePermissionTo($managerFinancePermissions);
        $user1 = User::find(1);
        $user1->assignRole('Super Admin');
        $user2 = User::find(2);
        $user2->assignRole('Admin Marketing');
        $user3 = User::find(3);
        $user3->assignRole('Admin Finance');
        $user4 = User::find(4);
        $user4->assignRole('Manager Marketing');
        $user5 = User::find(5);
        $user5->assignRole('Manager Finance');
        // $adminFarm->givePermissionTo($permissions);
        // $managerArea->givePermissionTo(['project.list', 'pembelian.submit']);
        // $staffAudit->givePermissionTo(['audit.access', 'pembelian.submit']);
    }
}
