<?php

namespace Database\Seeders;

use App\Models\PlanEntitlement;
use App\Models\Program;
use App\Models\SubscriptionPlan;
use App\Models\TestType;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $cpns = Program::where('code', 'CPNS')->first();
        $bumn = Program::where('code', 'BUMN')->first();
        $generic = TestType::where('code', 'GENERIC')->first();
        $disc = TestType::where('code', 'DISC')->first();
        $ist = TestType::where('code', 'IST')->first();
        $kraepelin = TestType::where('code', 'KRAEPELIN')->first();
        $psychotest = TestType::where('code', 'PSYCHOTEST')->first();

        // ── Plan: CPNS Basic ──
        $cpnsBasic = SubscriptionPlan::firstOrCreate(
            ['code' => 'CPNS-BASIC'],
            [
                'name' => 'CPNS Basic',
                'description' => 'Paket latihan CPNS dasar. Akses soal TWK, TIU, TKP dengan pembahasan.',
                'price' => 99000,
                'duration_days' => 30,
                'is_active' => true,
            ]
        );

        if ($cpns && $generic) {
            PlanEntitlement::firstOrCreate([
                'subscription_plan_id' => $cpnsBasic->id,
                'program_id' => $cpns->id,
                'test_type_id' => $generic->id,
                'access_type' => 'practice',
            ], ['limit_attempts' => null]);

            PlanEntitlement::firstOrCreate([
                'subscription_plan_id' => $cpnsBasic->id,
                'program_id' => $cpns->id,
                'test_type_id' => $generic->id,
                'access_type' => 'discussion',
            ], ['limit_attempts' => null]);
        }

        // ── Plan: CPNS Premium ──
        $cpnsPremium = SubscriptionPlan::firstOrCreate(
            ['code' => 'CPNS-PREMIUM'],
            [
                'name' => 'CPNS Premium',
                'description' => 'Paket premium CPNS. Akses penuh soal, try out, pembahasan, dan laporan detail.',
                'price' => 199000,
                'duration_days' => 90,
                'is_active' => true,
            ]
        );

        if ($cpns && $generic) {
            foreach (['practice', 'tryout', 'discussion', 'report'] as $access) {
                PlanEntitlement::firstOrCreate([
                    'subscription_plan_id' => $cpnsPremium->id,
                    'program_id' => $cpns->id,
                    'test_type_id' => $generic->id,
                    'access_type' => $access,
                ], ['limit_attempts' => null]);
            }
        }

        // ── Plan: BUMN Psikologi ──
        $bumnPsiko = SubscriptionPlan::firstOrCreate(
            ['code' => 'BUMN-PSIKO'],
            [
                'name' => 'BUMN Psikologi',
                'description' => 'Paket tes psikologi lengkap untuk seleksi BUMN. DISC, IST, Kraepelin, Psikotes.',
                'price' => 149000,
                'duration_days' => 30,
                'is_active' => true,
            ]
        );

        if ($bumn) {
            $bumnTestTypes = array_filter([$disc, $ist, $kraepelin, $psychotest]);
            foreach ($bumnTestTypes as $tt) {
                PlanEntitlement::firstOrCreate([
                    'subscription_plan_id' => $bumnPsiko->id,
                    'program_id' => $bumn->id,
                    'test_type_id' => $tt->id,
                    'access_type' => 'practice',
                ], ['limit_attempts' => null]);

                PlanEntitlement::firstOrCreate([
                    'subscription_plan_id' => $bumnPsiko->id,
                    'program_id' => $bumn->id,
                    'test_type_id' => $tt->id,
                    'access_type' => 'report',
                ], ['limit_attempts' => null]);
            }
        }

        // ── Plan: Bundling CPNS + BUMN ──
        $bundling = SubscriptionPlan::firstOrCreate(
            ['code' => 'BUNDLING-ALL'],
            [
                'name' => 'Bundling CPNS + BUMN',
                'description' => 'Paket lengkap semua program. Akses penuh soal CPNS dan tes psikologi BUMN.',
                'price' => 299000,
                'duration_days' => 180,
                'is_active' => true,
            ]
        );

        // CPNS entitlements for bundling
        if ($cpns && $generic) {
            foreach (['practice', 'tryout', 'discussion', 'report'] as $access) {
                PlanEntitlement::firstOrCreate([
                    'subscription_plan_id' => $bundling->id,
                    'program_id' => $cpns->id,
                    'test_type_id' => $generic->id,
                    'access_type' => $access,
                ], ['limit_attempts' => null]);
            }
        }

        // BUMN entitlements for bundling
        if ($bumn) {
            $bumnTestTypes = array_filter([$disc, $ist, $kraepelin, $psychotest]);
            foreach ($bumnTestTypes as $tt) {
                foreach (['practice', 'tryout', 'report'] as $access) {
                    PlanEntitlement::firstOrCreate([
                        'subscription_plan_id' => $bundling->id,
                        'program_id' => $bumn->id,
                        'test_type_id' => $tt->id,
                        'access_type' => $access,
                    ], ['limit_attempts' => null]);
                }
            }
        }
    }
}
