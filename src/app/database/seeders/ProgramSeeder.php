<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\TestType;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['code' => 'cpns', 'name' => 'CPNS/PNS Preparation', 'description' => 'Persiapan seleksi CPNS/PNS meliputi TWK, TIU, dan TKP', 'is_active' => true],
            ['code' => 'bumn', 'name' => 'BUMN Preparation', 'description' => 'Persiapan seleksi BUMN meliputi psikotes DISC, IST, Kraepelin, dan Papikostick', 'is_active' => true],
            ['code' => 'general', 'name' => 'General Psychotest', 'description' => 'Latihan psikotes umum untuk berbagai keperluan seleksi kerja', 'is_active' => true],
        ];

        foreach ($programs as $program) {
            Program::firstOrCreate(['code' => $program['code']], $program);
        }

        $testTypes = [
            ['code' => 'TWK', 'name' => 'Tes Wawasan Kebangsaan', 'engine_type' => 'generic', 'description' => 'Tes wawasan kebangsaan untuk CPNS', 'is_active' => true],
            ['code' => 'TIU', 'name' => 'Tes Intelegensia Umum', 'engine_type' => 'generic', 'description' => 'Tes intelegensia umum untuk CPNS', 'is_active' => true],
            ['code' => 'TKP', 'name' => 'Tes Karakteristik Pribadi', 'engine_type' => 'generic', 'description' => 'Tes karakteristik pribadi untuk CPNS', 'is_active' => true],
            ['code' => 'DISC', 'name' => 'DISC Personality Assessment', 'engine_type' => 'disc', 'description' => 'Tes kepribadian DISC (Dominance, Influence, Steadiness, Compliance)', 'is_active' => true],
            ['code' => 'IST', 'name' => 'Intelligenz Struktur Test', 'engine_type' => 'ist', 'description' => 'Tes struktur inteligensi dengan 9 subtest', 'is_active' => true],
            ['code' => 'KRAEPELIN', 'name' => 'Tes Kraepelin', 'engine_type' => 'kraepelin', 'description' => 'Tes kecepatan dan ketelitian aritmatika', 'is_active' => true],
            ['code' => 'PAPIKOSTICK', 'name' => 'Papikostick Personality Inventory', 'engine_type' => 'papikostick', 'description' => 'Tes inventori kepribadian Papikostick', 'is_active' => true],
        ];

        foreach ($testTypes as $testType) {
            TestType::firstOrCreate(['code' => $testType['code']], $testType);
        }

        // Map programs to test types
        $cpns = Program::where('code', 'cpns')->first();
        $bumn = Program::where('code', 'bumn')->first();
        $general = Program::where('code', 'general')->first();

        $cpns->testTypes()->syncWithoutDetaching(
            TestType::whereIn('code', ['TWK', 'TIU', 'TKP'])->pluck('id')->toArray()
        );

        $bumn->testTypes()->syncWithoutDetaching(
            TestType::whereIn('code', ['DISC', 'IST', 'KRAEPELIN', 'PAPIKOSTICK'])->pluck('id')->toArray()
        );

        $general->testTypes()->syncWithoutDetaching(
            TestType::whereIn('code', ['DISC', 'IST', 'KRAEPELIN', 'PAPIKOSTICK'])->pluck('id')->toArray()
        );
    }
}
