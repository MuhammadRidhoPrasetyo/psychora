<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\TestType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        // Programs
        $cpns = Program::firstOrCreate(
            ['code' => 'CPNS'],
            [
                'name' => 'Seleksi CPNS',
                'description' => 'Program latihan dan simulasi tes seleksi Calon Pegawai Negeri Sipil (CPNS). Meliputi Tes Wawasan Kebangsaan (TWK), Tes Intelegensia Umum (TIU), dan Tes Karakteristik Pribadi (TKP).',
                'is_active' => true,
            ]
        );

        $bumn = Program::firstOrCreate(
            ['code' => 'BUMN'],
            [
                'name' => 'Seleksi BUMN',
                'description' => 'Program latihan tes psikologi untuk seleksi rekrutmen Badan Usaha Milik Negara (BUMN). Meliputi tes DISC, IST, Kraepelin, dan psikotes lainnya.',
                'is_active' => true,
            ]
        );

        // Test Types
        $generic = TestType::firstOrCreate(
            ['code' => 'GENERIC'],
            [
                'name' => 'Tes Generik',
                'engine_type' => 'generic',
                'description' => 'Engine tes soal pilihan ganda standar (TWK, TIU, TKP).',
                'is_active' => true,
            ]
        );

        $disc = TestType::firstOrCreate(
            ['code' => 'DISC'],
            [
                'name' => 'Tes DISC',
                'engine_type' => 'disc',
                'description' => 'Tes kepribadian DISC (Dominance, Influence, Steadiness, Compliance).',
                'is_active' => true,
            ]
        );

        $ist = TestType::firstOrCreate(
            ['code' => 'IST'],
            [
                'name' => 'Tes IST',
                'engine_type' => 'ist',
                'description' => 'Intelligenz Struktur Test — tes inteligensi terstruktur dengan 9 subtes.',
                'is_active' => true,
            ]
        );

        $kraepelin = TestType::firstOrCreate(
            ['code' => 'KRAEPELIN'],
            [
                'name' => 'Tes Kraepelin',
                'engine_type' => 'kraepelin',
                'description' => 'Tes kecepatan dan ketelitian penjumlahan angka kolom (Kraepelin/Pauli).',
                'is_active' => true,
            ]
        );

        $psychotest = TestType::firstOrCreate(
            ['code' => 'PSYCHOTEST'],
            [
                'name' => 'Psikotes',
                'engine_type' => 'psychotest',
                'description' => 'Modul tes psikologi umum dengan penilaian aspek dan karakteristik.',
                'is_active' => true,
            ]
        );

        // Attach test types to programs (pivot has uuid PK, use DB::table)
        $this->attachTestType($cpns->id, $generic->id);
        $this->attachTestType($bumn->id, $disc->id);
        $this->attachTestType($bumn->id, $ist->id);
        $this->attachTestType($bumn->id, $kraepelin->id);
        $this->attachTestType($bumn->id, $psychotest->id);
    }

    private function attachTestType(string $programId, string $testTypeId): void
    {
        DB::table('program_test_types')->updateOrInsert(
            ['program_id' => $programId, 'test_type_id' => $testTypeId],
            [
                'id' => (string) Str::uuid(),
                'program_id' => $programId,
                'test_type_id' => $testTypeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
