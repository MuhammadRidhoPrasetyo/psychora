<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Test;
use App\Models\TestPackage;
use App\Models\TestPackageItem;
use App\Models\TestSection;
use App\Models\TestType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
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
        $admin = User::whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->first();

        // ════════════════════════════════════════
        //  CPNS Tests
        // ════════════════════════════════════════

        // ── Try Out CPNS SKD #1 ──
        $cpnsTest1 = Test::firstOrCreate(
            ['slug' => 'tryout-cpns-skd-1'],
            [
                'program_id' => $cpns->id,
                'test_type_id' => $generic->id,
                'title' => 'Try Out CPNS SKD #1',
                'description' => 'Simulasi ujian Seleksi Kompetensi Dasar CPNS. Terdiri dari TWK, TIU, dan TKP.',
                'instruction' => "Petunjuk Pengerjaan:\n1. Tes ini terdiri dari 3 kategori: TWK (30 soal), TIU (35 soal), TKP (35 soal)\n2. Waktu pengerjaan: 100 menit\n3. Pilih satu jawaban yang paling tepat\n4. Nilai benar: 5, Nilai salah: 0, Tidak dijawab: 0",
                'duration_minutes' => 100,
                'total_questions' => 100,
                'scoring_method' => 'weighted',
                'visibility' => 'premium',
                'status' => 'published',
                'created_by' => $admin?->id,
            ]
        );

        // CPNS Blueprint
        $this->createCpnsBlueprint($cpnsTest1->id, [
            ['category_code' => 'TWK', 'total_questions' => 30, 'passing_score' => 65],
            ['category_code' => 'TIU', 'total_questions' => 35, 'passing_score' => 80],
            ['category_code' => 'TKP', 'total_questions' => 35, 'passing_score' => 166],
        ]);

        // CPNS Sections
        $twkSection = $this->createSection($cpnsTest1->id, 'Tes Wawasan Kebangsaan (TWK)', 'Mengukur penguasaan pengetahuan dan kemampuan mengimplementasikan nilai-nilai Pancasila, UUD 1945, NKRI, dan Bhinneka Tunggal Ika.', 0);
        $tiuSection = $this->createSection($cpnsTest1->id, 'Tes Intelegensia Umum (TIU)', 'Mengukur kemampuan verbal, numerik, dan figural.', 1);
        $tkpSection = $this->createSection($cpnsTest1->id, 'Tes Karakteristik Pribadi (TKP)', 'Mengukur integritas, semangat berprestasi, kreativitas, kemampuan beradaptasi, dan orientasi pelayanan.', 2);

        // TWK Questions
        $this->createMultipleChoiceQuestions($cpnsTest1->id, $twkSection->id, $admin?->id, [
            [
                'content' => 'Pancasila sebagai dasar negara Indonesia pertama kali dirumuskan dalam sidang BPUPKI oleh...',
                'options' => [
                    ['key' => 'A', 'text' => 'Ir. Soekarno', 'correct' => true, 'score' => 5],
                    ['key' => 'B', 'text' => 'Drs. Moh. Hatta', 'correct' => false, 'score' => 0],
                    ['key' => 'C', 'text' => 'Mr. Soepomo', 'correct' => false, 'score' => 0],
                    ['key' => 'D', 'text' => 'Mr. Muh. Yamin', 'correct' => false, 'score' => 0],
                    ['key' => 'E', 'text' => 'KH. Agus Salim', 'correct' => false, 'score' => 0],
                ],
                'explanation' => 'Ir. Soekarno menyampaikan rumusan dasar negara yang kemudian dikenal sebagai Pancasila pada tanggal 1 Juni 1945 dalam sidang BPUPKI.',
                'difficulty' => 'easy',
            ],
            [
                'content' => 'Pasal dalam UUD 1945 yang mengatur tentang hak asasi manusia secara khusus terdapat pada...',
                'options' => [
                    ['key' => 'A', 'text' => 'Pasal 26', 'correct' => false, 'score' => 0],
                    ['key' => 'B', 'text' => 'Pasal 27', 'correct' => false, 'score' => 0],
                    ['key' => 'C', 'text' => 'Pasal 28A-28J', 'correct' => true, 'score' => 5],
                    ['key' => 'D', 'text' => 'Pasal 29', 'correct' => false, 'score' => 0],
                    ['key' => 'E', 'text' => 'Pasal 30', 'correct' => false, 'score' => 0],
                ],
                'explanation' => 'Pasal 28A sampai 28J UUD 1945 secara khusus mengatur tentang Hak Asasi Manusia, yang ditambahkan melalui amandemen kedua UUD 1945.',
                'difficulty' => 'easy',
            ],
            [
                'content' => 'Semboyan "Bhinneka Tunggal Ika" berasal dari kitab...',
                'options' => [
                    ['key' => 'A', 'text' => 'Negarakertagama', 'correct' => false, 'score' => 0],
                    ['key' => 'B', 'text' => 'Sutasoma', 'correct' => true, 'score' => 5],
                    ['key' => 'C', 'text' => 'Pararaton', 'correct' => false, 'score' => 0],
                    ['key' => 'D', 'text' => 'Arjunawiwaha', 'correct' => false, 'score' => 0],
                    ['key' => 'E', 'text' => 'Serat Centhini', 'correct' => false, 'score' => 0],
                ],
                'explanation' => 'Semboyan Bhinneka Tunggal Ika diambil dari kakawin Sutasoma karangan Mpu Tantular pada masa Kerajaan Majapahit.',
                'difficulty' => 'medium',
            ],
        ]);

        // TIU Questions
        $this->createMultipleChoiceQuestions($cpnsTest1->id, $tiuSection->id, $admin?->id, [
            [
                'content' => 'Jika semua mahasiswa rajin belajar, dan Andi adalah mahasiswa, maka...',
                'options' => [
                    ['key' => 'A', 'text' => 'Andi belum tentu rajin belajar', 'correct' => false, 'score' => 0],
                    ['key' => 'B', 'text' => 'Andi rajin belajar', 'correct' => true, 'score' => 5],
                    ['key' => 'C', 'text' => 'Andi tidak rajin belajar', 'correct' => false, 'score' => 0],
                    ['key' => 'D', 'text' => 'Andi mungkin rajin belajar', 'correct' => false, 'score' => 0],
                    ['key' => 'E', 'text' => 'Tidak dapat disimpulkan', 'correct' => false, 'score' => 0],
                ],
                'explanation' => 'Premis mayor: Semua mahasiswa rajin belajar. Premis minor: Andi adalah mahasiswa. Kesimpulan logis: Andi rajin belajar (silogisme kategoris).',
                'difficulty' => 'easy',
            ],
            [
                'content' => 'Jika 3x + 7 = 22, maka nilai x adalah...',
                'options' => [
                    ['key' => 'A', 'text' => '3', 'correct' => false, 'score' => 0],
                    ['key' => 'B', 'text' => '4', 'correct' => false, 'score' => 0],
                    ['key' => 'C', 'text' => '5', 'correct' => true, 'score' => 5],
                    ['key' => 'D', 'text' => '6', 'correct' => false, 'score' => 0],
                    ['key' => 'E', 'text' => '7', 'correct' => false, 'score' => 0],
                ],
                'explanation' => '3x + 7 = 22 → 3x = 15 → x = 5',
                'difficulty' => 'easy',
            ],
            [
                'content' => 'ANALOG : DIGITAL = ...',
                'options' => [
                    ['key' => 'A', 'text' => 'MANUAL : OTOMATIS', 'correct' => true, 'score' => 5],
                    ['key' => 'B', 'text' => 'KUNO : MODERN', 'correct' => false, 'score' => 0],
                    ['key' => 'C', 'text' => 'LAMBAT : CEPAT', 'correct' => false, 'score' => 0],
                    ['key' => 'D', 'text' => 'BESAR : KECIL', 'correct' => false, 'score' => 0],
                    ['key' => 'E', 'text' => 'LAMA : BARU', 'correct' => false, 'score' => 0],
                ],
                'explanation' => 'Analog : Digital memiliki hubungan kontradiksi dalam teknologi, sama seperti Manual : Otomatis.',
                'difficulty' => 'medium',
            ],
        ]);

        // TKP Questions (scoring: 1-5 graduated, no single correct answer)
        $this->createMultipleChoiceQuestions($cpnsTest1->id, $tkpSection->id, $admin?->id, [
            [
                'content' => 'Anda mengetahui rekan kerja Anda melakukan pelanggaran ringan di kantor. Apa yang akan Anda lakukan?',
                'options' => [
                    ['key' => 'A', 'text' => 'Langsung melaporkan ke atasan', 'correct' => false, 'score' => 4],
                    ['key' => 'B', 'text' => 'Mengingatkan rekan kerja secara pribadi dan memberi kesempatan memperbaiki', 'correct' => true, 'score' => 5],
                    ['key' => 'C', 'text' => 'Diam saja karena bukan urusan saya', 'correct' => false, 'score' => 1],
                    ['key' => 'D', 'text' => 'Membicarakannya dengan rekan kerja lain', 'correct' => false, 'score' => 2],
                    ['key' => 'E', 'text' => 'Menunggu sampai atasan mengetahui sendiri', 'correct' => false, 'score' => 3],
                ],
                'explanation' => 'Mengingatkan secara pribadi menunjukkan integritas dan kepedulian tanpa mempermalukan, sekaligus memberi kesempatan perbaikan.',
                'difficulty' => 'medium',
            ],
            [
                'content' => 'Anda diminta menyelesaikan tugas penting yang deadline-nya sangat ketat, sementara Anda juga memiliki tugas rutin. Langkah Anda...',
                'options' => [
                    ['key' => 'A', 'text' => 'Fokus pada tugas penting dan menunda tugas rutin', 'correct' => false, 'score' => 3],
                    ['key' => 'B', 'text' => 'Mengerjakan keduanya secara bergantian', 'correct' => false, 'score' => 4],
                    ['key' => 'C', 'text' => 'Membuat prioritas, mengerjakan tugas penting, dan mendelegasikan tugas rutin jika memungkinkan', 'correct' => true, 'score' => 5],
                    ['key' => 'D', 'text' => 'Meminta perpanjangan waktu untuk tugas penting', 'correct' => false, 'score' => 2],
                    ['key' => 'E', 'text' => 'Menolak tugas tambahan karena sudah sibuk', 'correct' => false, 'score' => 1],
                ],
                'explanation' => 'Membuat prioritas dan mendelegasikan menunjukkan kemampuan manajemen waktu dan leadership yang baik.',
                'difficulty' => 'medium',
            ],
        ]);

        // ── Try Out CPNS SKD #2 (Gratis) ──
        Test::firstOrCreate(
            ['slug' => 'tryout-cpns-skd-gratis'],
            [
                'program_id' => $cpns->id,
                'test_type_id' => $generic->id,
                'title' => 'Try Out CPNS SKD Gratis',
                'description' => 'Simulasi gratis ujian SKD CPNS. Cocok untuk mencoba platform sebelum berlangganan.',
                'instruction' => "Petunjuk Pengerjaan:\n1. Tes ini terdiri dari soal campuran TWK, TIU, dan TKP\n2. Waktu pengerjaan: 30 menit\n3. Pilih satu jawaban yang paling tepat",
                'duration_minutes' => 30,
                'total_questions' => 20,
                'scoring_method' => 'standard',
                'visibility' => 'free',
                'status' => 'published',
                'created_by' => $admin?->id,
            ]
        );

        // ════════════════════════════════════════
        //  BUMN Tests
        // ════════════════════════════════════════

        // DISC Test
        Test::firstOrCreate(
            ['slug' => 'disc-personality-test'],
            [
                'program_id' => $bumn->id,
                'test_type_id' => $disc->id,
                'title' => 'Tes Kepribadian DISC',
                'description' => 'Tes untuk mengidentifikasi tipe kepribadian berdasarkan model DISC (Dominance, Influence, Steadiness, Compliance).',
                'instruction' => "Petunjuk Pengerjaan:\n1. Pada setiap kelompok pernyataan, pilih satu yang PALING menggambarkan diri Anda (Most) dan satu yang PALING TIDAK menggambarkan diri Anda (Least)\n2. Jawab dengan jujur, tidak ada jawaban benar atau salah\n3. Waktu pengerjaan tidak dibatasi",
                'duration_minutes' => null,
                'total_questions' => 24,
                'scoring_method' => 'profile',
                'visibility' => 'premium',
                'status' => 'published',
                'created_by' => $admin?->id,
            ]
        );

        // IST Test
        Test::firstOrCreate(
            ['slug' => 'ist-intelligence-test'],
            [
                'program_id' => $bumn->id,
                'test_type_id' => $ist->id,
                'title' => 'Tes Inteligensi IST',
                'description' => 'Intelligenz Struktur Test (IST) untuk mengukur struktur inteligensi melalui 9 subtes.',
                'instruction' => "Petunjuk Pengerjaan:\n1. Tes terdiri dari beberapa subtes dengan waktu yang berbeda-beda\n2. Kerjakan setiap subtes sesuai petunjuk yang diberikan\n3. Waktu akan berjalan otomatis untuk setiap subtes",
                'duration_minutes' => 90,
                'total_questions' => 176,
                'scoring_method' => 'weighted',
                'visibility' => 'premium',
                'status' => 'published',
                'created_by' => $admin?->id,
            ]
        );

        // Kraepelin Test
        Test::firstOrCreate(
            ['slug' => 'kraepelin-test'],
            [
                'program_id' => $bumn->id,
                'test_type_id' => $kraepelin->id,
                'title' => 'Tes Kraepelin',
                'description' => 'Tes kecepatan dan ketelitian penjumlahan angka. Mengukur daya tahan, konsistensi, dan ketelitian.',
                'instruction' => "Petunjuk Pengerjaan:\n1. Jumlahkan dua angka yang berurutan dari bawah ke atas\n2. Tuliskan hasil penjumlahan (satuan saja jika hasilnya 2 digit) di antara kedua angka\n3. Setiap kolom akan berpindah otomatis sesuai waktu yang ditentukan",
                'duration_minutes' => 30,
                'total_questions' => null,
                'scoring_method' => 'manual',
                'visibility' => 'premium',
                'status' => 'published',
                'created_by' => $admin?->id,
            ]
        );

        // Psychotest
        Test::firstOrCreate(
            ['slug' => 'psychotest-general'],
            [
                'program_id' => $bumn->id,
                'test_type_id' => $psychotest->id,
                'title' => 'Psikotes Umum',
                'description' => 'Tes psikologi umum untuk mengukur berbagai aspek kepribadian dan potensi.',
                'instruction' => "Petunjuk Pengerjaan:\n1. Pilih jawaban yang paling sesuai dengan diri Anda\n2. Jawab dengan jujur, tidak ada jawaban benar atau salah\n3. Kerjakan semua soal",
                'duration_minutes' => 45,
                'total_questions' => 60,
                'scoring_method' => 'profile',
                'visibility' => 'premium',
                'status' => 'published',
                'created_by' => $admin?->id,
            ]
        );

        // ════════════════════════════════════════
        //  CPNS Score Rules
        // ════════════════════════════════════════
        if ($generic) {
            $this->createCpnsScoreRules($generic->id);
        }

        // ════════════════════════════════════════
        //  Test Packages
        // ════════════════════════════════════════
        $cpnsPackage = TestPackage::firstOrCreate(
            ['code' => 'PKG-CPNS-TRYOUT'],
            [
                'program_id' => $cpns->id,
                'name' => 'Paket Try Out CPNS SKD',
                'description' => 'Kumpulan try out simulasi SKD CPNS lengkap.',
                'is_premium' => true,
                'is_active' => true,
            ]
        );

        // Attach tests to package
        $cpnsTests = Test::where('program_id', $cpns->id)->get();
        foreach ($cpnsTests as $i => $test) {
            TestPackageItem::firstOrCreate([
                'test_package_id' => $cpnsPackage->id,
                'test_id' => $test->id,
            ], ['sort_order' => $i]);
        }

        $bumnPackage = TestPackage::firstOrCreate(
            ['code' => 'PKG-BUMN-PSIKO'],
            [
                'program_id' => $bumn->id,
                'name' => 'Paket Tes Psikologi BUMN',
                'description' => 'Kumpulan tes psikologi untuk persiapan seleksi BUMN.',
                'is_premium' => true,
                'is_active' => true,
            ]
        );

        $bumnTests = Test::where('program_id', $bumn->id)->get();
        foreach ($bumnTests as $i => $test) {
            TestPackageItem::firstOrCreate([
                'test_package_id' => $bumnPackage->id,
                'test_id' => $test->id,
            ], ['sort_order' => $i]);
        }
    }

    private function createSection(string $testId, string $title, ?string $instruction, int $sortOrder): TestSection
    {
        return TestSection::firstOrCreate(
            ['test_id' => $testId, 'title' => $title],
            [
                'instruction' => $instruction,
                'sort_order' => $sortOrder,
            ]
        );
    }

    private function createCpnsBlueprint(string $testId, array $blueprints): void
    {
        foreach ($blueprints as $bp) {
            DB::table('cpns_test_blueprints')->updateOrInsert(
                ['test_id' => $testId, 'category_code' => $bp['category_code']],
                array_merge($bp, [
                    'id' => (string) Str::uuid(),
                    'test_id' => $testId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    private function createCpnsScoreRules(string $testTypeId): void
    {
        $rules = [
            ['category_code' => 'TWK', 'correct_score' => 5, 'wrong_score' => 0, 'empty_score' => 0],
            ['category_code' => 'TIU', 'correct_score' => 5, 'wrong_score' => 0, 'empty_score' => 0],
            ['category_code' => 'TKP', 'correct_score' => 5, 'wrong_score' => 0, 'empty_score' => 0],
        ];

        foreach ($rules as $rule) {
            DB::table('cpns_score_rules')->updateOrInsert(
                ['test_type_id' => $testTypeId, 'category_code' => $rule['category_code']],
                array_merge($rule, [
                    'id' => (string) Str::uuid(),
                    'test_type_id' => $testTypeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    private function createMultipleChoiceQuestions(string $testId, string $sectionId, ?int $createdBy, array $questions): void
    {
        foreach ($questions as $i => $q) {
            $question = DB::table('questions')->where('test_id', $testId)
                ->where('test_section_id', $sectionId)
                ->where('content', $q['content'])
                ->first();

            if (!$question) {
                $questionId = (string) Str::uuid();
                DB::table('questions')->insert([
                    'id' => $questionId,
                    'test_id' => $testId,
                    'test_section_id' => $sectionId,
                    'question_type' => 'multiple_choice',
                    'content' => $q['content'],
                    'explanation' => $q['explanation'] ?? null,
                    'difficulty' => $q['difficulty'] ?? 'medium',
                    'score' => 5,
                    'sort_order' => $i,
                    'is_active' => true,
                    'created_by' => $createdBy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($q['options'] as $j => $opt) {
                    DB::table('question_options')->insert([
                        'id' => (string) Str::uuid(),
                        'question_id' => $questionId,
                        'option_key' => $opt['key'],
                        'content' => $opt['text'],
                        'is_correct' => $opt['correct'],
                        'score' => $opt['score'],
                        'sort_order' => $j,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
