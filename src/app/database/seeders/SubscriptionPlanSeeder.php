<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'code' => 'free',
                'name' => 'Free',
                'description' => 'Akses terbatas ke soal latihan gratis',
                'price' => 0,
                'duration_days' => 365,
                'is_active' => true,
            ],
            [
                'code' => 'cpns-basic',
                'name' => 'CPNS Basic',
                'description' => 'Akses latihan CPNS dasar meliputi TWK, TIU, dan TKP',
                'price' => 99000,
                'duration_days' => 30,
                'is_active' => true,
            ],
            [
                'code' => 'cpns-premium',
                'name' => 'CPNS Premium',
                'description' => 'Akses penuh latihan dan tryout CPNS dengan pembahasan lengkap',
                'price' => 199000,
                'duration_days' => 90,
                'is_active' => true,
            ],
            [
                'code' => 'bumn-basic',
                'name' => 'BUMN Basic',
                'description' => 'Akses latihan psikotes BUMN dasar (DISC + IST)',
                'price' => 149000,
                'duration_days' => 30,
                'is_active' => true,
            ],
            [
                'code' => 'bumn-premium',
                'name' => 'BUMN Premium',
                'description' => 'Akses penuh psikotes BUMN termasuk Kraepelin dan Papikostick',
                'price' => 299000,
                'duration_days' => 90,
                'is_active' => true,
            ],
            [
                'code' => 'all-access',
                'name' => 'All Access',
                'description' => 'Akses penuh semua program CPNS dan BUMN',
                'price' => 399000,
                'duration_days' => 90,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(['code' => $plan['code']], $plan);
        }
    }
}
