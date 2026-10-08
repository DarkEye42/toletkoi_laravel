<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPreference;
use App\Models\CreditWallet;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@toletkoi.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdmin->syncRoles($superAdminRole);
        }

        // Create Super Admin Profile
        UserProfile::updateOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'phone_verified_at' => now(),
            ]
        );

        // Create User Preferences
        UserPreference::updateOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'language' => 'en',
                'theme' => 'light',
                'currency' => 'BDT',
                'email_notifications' => true,
                'sms_notifications' => true,
                'push_notifications' => true,
            ]
        );

        // Create Credit Wallet
        CreditWallet::updateOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'balance' => 0,
                'total_earned' => 0,
                'total_spent' => 0,
                'total_purchased' => 0,
                'total_refunded' => 0,
            ]
        );

        // Create a demo Landlord
        $landlord = User::updateOrCreate(
            ['email' => 'landlord@toletkoi.com'],
            [
                'name' => 'Demo Landlord',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $landlordRole = Role::where('name', 'Landlord')->first();
        if ($landlordRole) {
            $landlord->syncRoles($landlordRole);
        }

        UserProfile::updateOrCreate(
            ['user_id' => $landlord->id],
            [
                'phone_verified_at' => now(),
            ]
        );

        CreditWallet::updateOrCreate(
            ['user_id' => $landlord->id],
            [
                'balance' => 500,
                'total_earned' => 500,
                'total_spent' => 0,
                'total_purchased' => 500,
                'total_refunded' => 0,
            ]
        );

        // Create a demo Tenant
        $tenant = User::updateOrCreate(
            ['email' => 'tenant@toletkoi.com'],
            [
                'name' => 'Demo Tenant',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $tenantRole = Role::where('name', 'Tenant')->first();
        if ($tenantRole) {
            $tenant->syncRoles($tenantRole);
        }

        UserProfile::updateOrCreate(
            ['user_id' => $tenant->id],
            [
                'phone_verified_at' => now(),
            ]
        );

        CreditWallet::updateOrCreate(
            ['user_id' => $tenant->id],
            [
                'balance' => 100,
                'total_earned' => 100,
                'total_spent' => 0,
                'total_purchased' => 100,
                'total_refunded' => 0,
            ]
        );

        // Create a demo Agent
        $agent = User::updateOrCreate(
            ['email' => 'agent@toletkoi.com'],
            [
                'name' => 'Demo Agent',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $agentRole = Role::where('name', 'Agent')->first();
        if ($agentRole) {
            $agent->syncRoles($agentRole);
        }

        UserProfile::updateOrCreate(
            ['user_id' => $agent->id],
            [
                'phone_verified_at' => now(),
            ]
        );

        CreditWallet::updateOrCreate(
            ['user_id' => $agent->id],
            [
                'balance' => 1000,
                'total_earned' => 1000,
                'total_spent' => 0,
                'total_purchased' => 1000,
                'total_refunded' => 0,
            ]
        );

        $this->command->info('Users seeded successfully!');
        $this->command->info('  Super Admin: admin@toletkoi.com / password');
        $this->command->info('  Landlord:    landlord@toletkoi.com / password');
        $this->command->info('  Tenant:      tenant@toletkoi.com / password');
        $this->command->info('  Agent:       agent@toletkoi.com / password');
    }
}
