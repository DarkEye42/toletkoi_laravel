<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =====================================================
        // PERMISSIONS
        // =====================================================

        $permissions = [
            // Property management
            'manage own listings',
            'view all listings',
            'edit any listing',
            'delete any listing',
            'approve listings',
            'feature listings',
            'promote listings',

            // User management
            'manage users',
            'view users',
            'edit users',
            'delete users',
            'verify users',
            'manage roles',

            // Financial
            'manage credit packages',
            'manage subscription plans',
            'view transactions',
            'process refunds',
            'manage payments',

            // Content
            'manage cms pages',
            'manage blog posts',
            'manage blog comments',
            'manage site settings',

            // Property management (SaaS)
            'manage organizations',
            'manage buildings',
            'manage units',
            'manage tenants',
            'manage maintenance',
            'manage expenses',
            'view reports',

            // Services
            'manage service categories',
            'manage service providers',

            // KoiFind
            'manage koifind requests',
            'assign agents',

            // System
            'view audit logs',
            'view analytics',
            'manage verifications',

            // Reports
            'view dashboard',
            'view financial reports',
            'view property reports',
            'export data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // =====================================================
        // ROLES
        // =====================================================

        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Full system access with all permissions',
                'is_switchable' => false,
                'is_active' => true,
                'permissions' => $permissions, // All permissions
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'System administrator with broad access',
                'is_switchable' => false,
                'is_active' => true,
                'permissions' => array_filter($permissions, fn($p) => !in_array($p, ['manage roles'])),
            ],
            [
                'name' => 'Landlord',
                'slug' => 'landlord',
                'description' => 'Property owner who lists properties for rent/sale',
                'is_switchable' => true,
                'is_active' => true,
                'permissions' => [
                    'manage own listings',
                    'view all listings',
                    'manage organizations',
                    'manage buildings',
                    'manage units',
                    'manage tenants',
                    'manage maintenance',
                    'manage expenses',
                    'view reports',
                    'view dashboard',
                ],
            ],
            [
                'name' => 'Tenant',
                'slug' => 'tenant',
                'description' => 'Person looking for rental property',
                'is_switchable' => true,
                'is_active' => true,
                'permissions' => [
                    'view all listings',
                ],
            ],
            [
                'name' => 'Agent',
                'slug' => 'agent',
                'description' => 'Real estate agent or broker',
                'is_switchable' => true,
                'is_active' => true,
                'permissions' => [
                    'manage own listings',
                    'view all listings',
                    'manage koifind requests',
                    'view reports',
                    'view dashboard',
                ],
            ],
            [
                'name' => 'Service Provider',
                'slug' => 'service_provider',
                'description' => 'Provider of rental-related services',
                'is_switchable' => true,
                'is_active' => true,
                'permissions' => [
                    'view all listings',
                    'view dashboard',
                ],
            ],
            [
                'name' => 'Premium Landlord',
                'slug' => 'premium_landlord',
                'description' => 'Landlord with premium subscription benefits',
                'is_switchable' => true,
                'is_active' => true,
                'permissions' => [
                    'manage own listings',
                    'view all listings',
                    'feature listings',
                    'promote listings',
                    'manage organizations',
                    'manage buildings',
                    'manage units',
                    'manage tenants',
                    'manage maintenance',
                    'manage expenses',
                    'view reports',
                    'view dashboard',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => 'web'],
                [
                    'slug' => $roleData['slug'],
                    'description' => $roleData['description'],
                    'is_switchable' => $roleData['is_switchable'],
                    'is_active' => $roleData['is_active'],
                ]
            );

            // Sync permissions
            $role->syncPermissions($roleData['permissions']);
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
