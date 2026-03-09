<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ProgramSeeder::class,
            SubscriptionPlanSeeder::class,
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@psychora.id',
            'is_active' => true,
        ]);

        // Assign super_admin role
        \DB::table('model_has_roles')->insert([
            'role_id' => \App\Models\Role::where('name', 'super_admin')->first()->id,
            'model_type' => User::class,
            'model_id' => $admin->id,
        ]);

        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@psychora.id',
            'is_active' => true,
        ]);

        \DB::table('model_has_roles')->insert([
            'role_id' => \App\Models\Role::where('name', 'user')->first()->id,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
    }
}
