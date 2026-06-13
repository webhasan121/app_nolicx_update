<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDefaultAccess();
        $this->seedDefaultLocation();
    }

    private function seedDefaultAccess(): void
    {
        if (! Schema::hasTable('roles') || ! Schema::hasTable('permissions')) {
            return;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate('user', 'web');
        $userRole = Role::findOrCreate('user', 'web');
        Permission::findOrCreate('access_users_dashboard', 'web');
        $dashboardPermission = Permission::findOrCreate('access_vendor_dashboard', 'web');

        $userRole->givePermissionTo($dashboardPermission);
    }

    private function seedDefaultLocation(): void
    {
        if (! Schema::hasTable('countries') || ! Schema::hasTable('states') || ! Schema::hasTable('cities')) {
            return;
        }

        DB::table('countries')->updateOrInsert(
            ['id' => 18],
            [
                'name' => 'Bangladesh',
                'iso3' => 'BGD',
                'numeric_code' => '050',
                'iso2' => 'BD',
                'phonecode' => 880,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('states')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Dhaka',
                'country_id' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('cities')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Dhaka',
                'state_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
