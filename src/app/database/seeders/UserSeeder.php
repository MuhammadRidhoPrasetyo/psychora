<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@psychora.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '081200000001',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole && !$superAdmin->roles()->where('role_id', $superAdminRole->id)->exists()) {
            $superAdmin->roles()->attach($superAdminRole);
        }

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@psychora.id'],
            [
                'name' => 'Admin Psychora',
                'password' => Hash::make('password'),
                'phone' => '081200000002',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole);
        }

        // Demo User
        $user = User::firstOrCreate(
            ['email' => 'user@psychora.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'phone' => '081300000001',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $userRole = Role::where('name', 'user')->first();
        if ($userRole && !$user->roles()->where('role_id', $userRole->id)->exists()) {
            $user->roles()->attach($userRole);
        }

        // Create user profile for demo user
        UserProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'birth_date' => '1998-05-15',
                'gender' => 'male',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'education_level' => 'S1',
                'target_program' => 'cpns',
            ]
        );

        // Additional demo users
        $demoUsers = [
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@psychora.id',
                'phone' => '081300000002',
                'profile' => [
                    'birth_date' => '1999-08-20',
                    'gender' => 'female',
                    'province' => 'Jawa Barat',
                    'city' => 'Bandung',
                    'education_level' => 'S1',
                    'target_program' => 'bumn',
                ],
            ],
            [
                'name' => 'Ahmad Rizki',
                'email' => 'ahmad@psychora.id',
                'phone' => '081300000003',
                'profile' => [
                    'birth_date' => '2000-01-10',
                    'gender' => 'male',
                    'province' => 'Jawa Timur',
                    'city' => 'Surabaya',
                    'education_level' => 'S1',
                    'target_program' => 'cpns',
                ],
            ],
        ];

        foreach ($demoUsers as $demoUser) {
            $profile = $demoUser['profile'];
            unset($demoUser['profile']);

            $created = User::firstOrCreate(
                ['email' => $demoUser['email']],
                array_merge($demoUser, [
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ])
            );

            if ($userRole && !$created->roles()->where('role_id', $userRole->id)->exists()) {
                $created->roles()->attach($userRole);
            }

            UserProfile::firstOrCreate(
                ['user_id' => $created->id],
                $profile
            );
        }
    }
}
