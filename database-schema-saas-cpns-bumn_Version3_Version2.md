# Database Schema SaaS CPNS & BUMN

> Dokumentasi database untuk platform SaaS latihan tes CPNS/PNS dan BUMN.
> Sistem berbasis user account, subscription, dan modular test engine.
> Stack target: Laravel + MySQL + Inertia + Vue.js

---

# 1. Tujuan Desain Database

Database ini dirancang untuk mendukung:

- autentikasi user SaaS
- subscription & payment
- program belajar CPNS dan BUMN
- test engine generik
- modul test spesifik:
  - CPNS (TWK, TIU, TKP)
  - DISC
  - IST
  - Kraepelin
  - Psychotest (pengganti Papikostick, lebih generik)
- hasil pengerjaan dan analitik

---

# 2. Prinsip Arsitektur Database

## 2.1 Core + Specialized Modules
Database dibagi menjadi:
- **core tables** untuk kebutuhan umum
- **specialized tables** untuk test tertentu yang punya logika unik

## 2.2 User-based SaaS
Semua akses berbasis:
- akun user
- paket langganan
- entitlement/akses konten

## 2.3 Extensible
Jenis tes baru harus bisa ditambahkan tanpa mengubah struktur besar.

---

# 3. Modul Database

| Modul | Deskripsi |
|---|---|
| User & Access | akun, profil, role |
| Subscription & Billing | paket, langganan, pembayaran |
| Program & Catalog | CPNS, BUMN, kategori, paket tes |
| Generic Test Engine | tests, sections, questions, attempts |
| CPNS Module | blueprint TWK/TIU/TKP |
| DISC Module | form, question, option, scoring |
| IST Module | form, subtest, form_items, instructions, clues, result |
| Kraepelin Module | form, attempt columns/numbers, answer |
| Psychotest Module | form, aspects, characteristics, scoring, result (pengganti Papikostick) |
| Reporting & Logs | hasil, progress, activity logs |

---

# 4. Core Tables

## 4.1 users
Data akun user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| name | varchar | nama user |
| email | varchar | unique |
| email_verified_at | timestamp | nullable |
| password | varchar | hashed |
| phone | varchar | nullable |
| is_active | boolean | default true |
| created_at | timestamp |  |
| updated_at | timestamp |  |

**Indexes**
- PK: `id`
- Unique: `email`

---

## 4.2 user_profiles
Profil tambahan user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| user_id | char(36) | FK users.id |
| birth_date | date | nullable |
| gender | enum | nullable |
| province | varchar | nullable |
| city | varchar | nullable |
| education_level | varchar | nullable |
| target_program | enum | cpns/bumn/general |
| created_at | timestamp |  |
| updated_at | timestamp |  |

---

## 4.3 roles
Master role.

| Column | Type |
|---|---|
| id | bigint |
| name | varchar |
| guard_name | varchar |
| created_at | timestamp |
| updated_at | timestamp |

---

## 4.4 model_has_roles
Pivot role user/admin.

| Column | Type |
|---|---|
| role_id | bigint |
| model_type | varchar |
| model_id | char(36) |

---

# 5. Subscription & Billing

## 5.1 subscription_plans
Master paket langganan.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| code | varchar | unique |
| name | varchar | |
| description | text | nullable |
| price | decimal(12,2) | |
| duration_days | int | |
| is_active | boolean | |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 5.2 subscriptions
Langganan user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| user_id | char(36) | FK users.id |
| subscription_plan_id | char(36) | FK subscription_plans.id |
| start_at | datetime | |
| end_at | datetime | |
| status | enum | pending/active/expired/cancelled |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 5.3 payments
Data pembayaran.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| user_id | char(36) | FK users.id |
| subscription_id | char(36) | FK subscriptions.id nullable |
| invoice_number | varchar | unique |
| amount | decimal(12,2) | |
| payment_method | varchar | |
| payment_gateway | varchar | nullable |
| gateway_reference | varchar | nullable |
| status | enum | pending/paid/failed/expired/refunded |
| paid_at | datetime | nullable |
| payload | json | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 5.4 plan_entitlements
Hak akses plan.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| subscription_plan_id | char(36) | FK subscription_plans.id |
| program_id | char(36) | FK programs.id nullable |
| test_type_id | char(36) | FK test_types.id nullable |
| access_type | enum | practice/tryout/discussion/report |
| limit_attempts | int | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

---

# 6. Program & Catalog

## 6.1 programs
Jalur belajar.

Contoh:
- CPNS
- BUMN
- General Psychotest

| Column | Type |
|---|---|
| id | char(36) |
| code | varchar |
| name | varchar |
| description | text |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

---

## 6.2 test_types
Jenis tes.

Contoh:
- TWK
- TIU
- TKP
- DISC
- IST
- KRAEPELIN
- PSYCHOTEST

| Column | Type |
|---|---|
| id | char(36) |
| code | varchar |
| name | varchar |
| engine_type | enum |
| description | text |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

**engine_type**
- generic
- disc
- ist
- kraepelin
- psychotest

---

## 6.3 program_test_types
Mapping program ke jenis tes.

| Column | Type |
|---|---|
| id | char(36) |
| program_id | char(36) |
| test_type_id | char(36) |
| created_at | timestamp |
| updated_at | timestamp |

---

## 6.4 test_packages
Paket latihan / bundling konten.

| Column | Type |
|---|---|
| id | char(36) |
| program_id | char(36) |
| code | varchar |
| name | varchar |
| description | text |
| is_premium | boolean |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

---

## 6.5 test_package_items
Isi paket.

| Column | Type |
|---|---|
| id | char(36) |
| test_package_id | char(36) |
| test_id | char(36) |
| sort_order | int |
| created_at | timestamp |
| updated_at | timestamp |

---

# 7. Generic Test Engine

## 7.1 tests
Unit simulasi / exam.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| program_id | char(36) | FK programs.id |
| test_type_id | char(36) | FK test_types.id |
| title | varchar | |
| slug | varchar | unique |
| description | text | nullable |
| instruction | longtext | nullable |
| duration_minutes | int | nullable |
| total_questions | int | nullable |
| scoring_method | enum | standard/weighted/profile/manual |
| visibility | enum | free/premium/private |
| status | enum | draft/published/archived |
| created_by | char(36) | FK users.id nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 7.2 test_sections
Section dalam test.

| Column | Type |
|---|---|
| id | char(36) |
| test_id | char(36) |
| title | varchar |
| instruction | longtext |
| duration_minutes | int |
| sort_order | int |
| created_at | timestamp |
| updated_at | timestamp |

---

## 7.3 questions
Bank soal generik.

| Column | Type |
|---|---|
| id | char(36) |
| test_id | char(36) |
| test_section_id | char(36) nullable |
| question_type | enum |
| content | longtext |
| media_url | varchar nullable |
| explanation | longtext nullable |
| difficulty | enum nullable |
| score | decimal(8,2) |
| sort_order | int |
| is_active | boolean |
| created_by | char(36) nullable |
| created_at | timestamp |
| updated_at | timestamp |

**question_type**
- multiple_choice
- multi_select
- essay
- true_false
- number_input
- matrix

---

## 7.4 question_options
Opsi jawaban untuk generic engine.

| Column | Type |
|---|---|
| id | char(36) |
| question_id | char(36) |
| option_key | varchar |
| content | longtext |
| is_correct | boolean |
| score | decimal(8,2) nullable |
| sort_order | int |
| created_at | timestamp |
| updated_at | timestamp |

---

## 7.5 question_essay_answers
Answer key untuk pertanyaan essay (untuk auto-grading atau reference).

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| question_id | char(36) | FK questions.id |
| answer_text | varchar | teks jawaban yang diterima |
| score | int | nilai skor untuk jawaban ini |
| match_type | enum | exact/fuzzy/contains/regex |
| priority | int | prioritas jika multiple answers match |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `question_id`
- Index: `question_id`, `priority`

**Business Rules:**
- Satu pertanyaan dapat memiliki multiple acceptable answers
- `priority` menentukan urutan pengecekan jawaban
- `match_type`: exact (persis), fuzzy (toleransi typo), contains (kata kunci), regex (pattern)
- Higher score untuk jawaban lebih lengkap/akurat

---

## 7.6 test_attempts
Attempt user untuk test.

| Column | Type |
|---|---|
| id | char(36) |
| user_id | char(36) |
| test_id | char(36) |
| attempt_no | int |
| started_at | datetime nullable |
| submitted_at | datetime nullable |
| expired_at | datetime nullable |
| status | enum |
| total_score | decimal(10,2) nullable |
| percentage | decimal(5,2) nullable |
| result_payload | json nullable |
| created_at | timestamp |
| updated_at | timestamp |

**status**
- draft
- in_progress
- submitted
- expired
- evaluated

---

## 7.7 attempt_answers
Jawaban generic.

| Column | Type |
|---|---|
| id | char(36) |
| test_attempt_id | char(36) |
| question_id | char(36) |
| selected_option_id | char(36) nullable |
| answer_text | longtext nullable |
| answer_json | json nullable |
| is_correct | boolean nullable |
| score | decimal(8,2) nullable |
| answered_at | datetime nullable |
| created_at | timestamp |
| updated_at | timestamp |

---

## 7.8 test_results
Ringkasan hasil per attempt.

| Column | Type |
|---|---|
| id | char(36) |
| test_attempt_id | char(36) |
| user_id | char(36) |
| test_id | char(36) |
| raw_score | decimal(10,2) nullable |
| final_score | decimal(10,2) nullable |
| percentage | decimal(5,2) nullable |
| interpretation | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

---

# 8. CPNS Specific Tables

## 8.1 cpns_test_blueprints
Blueprint komposisi tes CPNS.

| Column | Type |
|---|---|
| id | char(36) |
| test_id | char(36) |
| category_code | enum |
| total_questions | int |
| passing_score | int nullable |
| created_at | timestamp |
| updated_at | timestamp |

**category_code**
- TWK
- TIU
- TKP

## 8.2 cpns_score_rules
Rule skor per kategori.

| Column | Type |
|---|---|
| id | char(36) |
| test_type_id | char(36) |
| category_code | enum |
| correct_score | decimal(8,2) |
| wrong_score | decimal(8,2) |
| empty_score | decimal(8,2) |
| created_at | timestamp |
| updated_at | timestamp |

---

# 9. DISC Module

> **Disesuaikan dengan arsitektur `database-schema-employee-testing.md`.**
> Perubahan utama:
> - `disc_options` tidak lagi menyimpan `disc_dimension` secara langsung
> - Ditambahkan tabel `disc_option_scorings` untuk mapping option ke D/I/S/C dengan response_type (most/least)
> - `disc_results` diperkaya dengan detail skor most/least per dimensi

---

## 9.1 disc_forms
Form/template untuk DISC test.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| test_id | char(36) | FK tests.id |
| name | varchar | nama form DISC |
| description | text | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `test_id`

**Business Rules:**
- DISC test menggunakan forced-choice format (Most/Least)
- Setiap question memiliki 4 options
- Output: skor D, I, S, C untuk profiling

---

## 9.2 disc_questions
Pertanyaan dalam DISC test.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| disc_form_id | char(36) | FK disc_forms.id |
| number | int | nomor urut pertanyaan |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `disc_form_id`
- Index: `number`

**Business Rules:**
- Setiap question harus memiliki exactly 4 options
- User harus memilih 1 Most dan 1 Least
- Tidak boleh memilih option yang sama untuk Most dan Least

---

## 9.3 disc_options
Pilihan jawaban untuk DISC test questions (4 options per question).

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| disc_question_id | char(36) | FK disc_questions.id |
| option_text | text | teks pernyataan option |
| sort_order | int | urutan tampilan |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `disc_question_id`
- Index: `sort_order`

**Business Rules:**
- Setiap question harus memiliki exactly 4 options
- Scoring di-mapping melalui tabel `disc_option_scorings`
- Option text berupa trait descriptor

---

## 9.4 disc_option_scorings
Scoring rules untuk DISC options (mapping option ke DISC codes berdasarkan response type).

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| disc_option_id | char(36) | FK disc_options.id |
| response_type | enum | most/least |
| disc_code | enum | D/I/S/C/star |
| score_value | int | nilai skor untuk kombinasi ini |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `disc_option_id`
- Index: `(disc_option_id, response_type)`, `disc_code`

**Enum Values:**
- `response_type`: 'most', 'least'
- `disc_code`: 'D', 'I', 'S', 'C', 'star'

**Business Rules:**
- Setiap option memiliki 2 scorings: Most dan Least
- `disc_code` 'star' untuk neutral/tidak affect skor
- Typical scoring: Most=+1, Least=-1 (atau variasi lainnya)
- Score calculation: sum all scores by DISC code
- Final DISC profile: (Most_D - Least_D), (Most_I - Least_I), dst.

---

## 9.5 disc_attempts
Record attempt DISC test oleh user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| test_attempt_id | char(36) | FK test_attempts.id |
| disc_form_id | char(36) | FK disc_forms.id |
| attempt_number | int | nomor percobaan |
| status | enum | not_started/in_progress/submitted/expired |
| started_at | datetime | nullable |
| submitted_at | datetime | nullable |
| deadline_at | datetime | nullable |
| score | decimal(10,2) | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `test_attempt_id`, `disc_form_id`
- Index: `status`

---

## 9.6 disc_answers
Jawaban DISC test user (Most/Least selection).

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| disc_attempt_id | char(36) | FK disc_attempts.id |
| disc_question_id | char(36) | FK disc_questions.id |
| most_option_id | char(36) | FK disc_options.id (pilihan Most) |
| least_option_id | char(36) | FK disc_options.id (pilihan Least) |
| answered_at | datetime | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `disc_attempt_id`, `disc_question_id`, `most_option_id`, `least_option_id`

---

## 9.7 disc_results
Skor DISC test user dengan detail per dimensi dan response type.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| disc_attempt_id | char(36) | FK disc_attempts.id |
| most_d | int | skor D pada bagian Most |
| most_i | int | skor I pada bagian Most |
| most_s | int | skor S pada bagian Most |
| most_c | int | skor C pada bagian Most |
| most_star | int | skor Star pada bagian Most |
| least_d | int | skor D pada bagian Least |
| least_i | int | skor I pada bagian Least |
| least_s | int | skor S pada bagian Least |
| least_c | int | skor C pada bagian Least |
| least_star | int | skor Star pada bagian Least |
| score_d | int | skor D final (Most_D - Least_D) |
| score_i | int | skor I final (Most_I - Least_I) |
| score_s | int | skor S final (Most_S - Least_S) |
| score_c | int | skor C final (Most_C - Least_C) |
| dominant_profile | varchar | nullable, profil dominan |
| interpretation | text | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `disc_attempt_id`

**Business Rules:**
- `score_d` = `most_d` - `least_d` (dan seterusnya untuk I, S, C)
- `dominant_profile` diisi dengan dimensi tertinggi (contoh: "DI", "SC")
- `most_star` dan `least_star` untuk tracking skor netral

---

# 10. IST Module

> **Disesuaikan dengan arsitektur `database-schema-employee-testing.md`.**
> Perubahan utama:
> - Ditambahkan tabel `ist_form_items` (pivot config per subtest dalam form)
> - Ditambahkan tabel `ist_instructions` (instruksi per form/subtest)
> - Ditambahkan tabel `ist_clues` (clue/hints per form/subtest)
> - `ist_subtests` diperkaya dengan `max_score`
> - `ist_attempts` diperkaya dengan `iq_score`
> - `ist_subtest_attempts` diperkaya dengan `random_seed`

---

## 10.1 ist_forms
Form IST (Intelligenz-Struktur-Test) yang merupakan grup dari 9 subtest.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| test_id | char(36) | FK tests.id |
| name | varchar | nama form IST |
| description | text | nullable |
| is_active | boolean | default true |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `test_id`
- Index: `is_active`

**Business Rules:**
- Setiap form harus memiliki 9 subtest (SE, WA, AN, GE, ME, RA, ZR, FA, WU)
- Form aktif dapat digunakan dalam test session
- Form inactive tidak dapat dipilih untuk test baru

---

## 10.2 ist_subtests
Item/subtest dalam IST (total 9 subtest).

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| ist_form_id | char(36) | FK ist_forms.id |
| subtest_code | varchar | kode: SE, WA, AN, GE, ME, RA, ZR, FA, WU |
| subtest_name | varchar | nama subtest |
| sort_order | int | urutan subtest (1-9) |
| duration_minutes | int | nullable, durasi per subtest dalam menit |
| max_score | int | nullable, skor maksimal untuk subtest ini |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `ist_form_id`
- Index: `subtest_code`, `sort_order`

**Subtest Codes & Categories:**

| Code | Name | Category | Description |
|---|---|---|---|
| SE | Sentence Completion | Verbal | Melengkapi kalimat |
| WA | Word Selection | Verbal | Pemilihan kata |
| AN | Analogies | Verbal | Analogi |
| GE | Similarities | Verbal | Kesamaan |
| ME | Arithmetic | Numerical | Perhitungan |
| RA | Number Series | Numerical | Deret angka |
| ZR | Numerical Reasoning | Numerical | Penalaran numerik |
| FA | Cube Tasks | Figural | Tugas kubus |
| WU | Memory Tasks | Figural | Tugas memori |

**Business Rules:**
- `sort_order` harus berurutan 1-9
- `subtest_code` harus valid dan unique per form
- Semua 9 subtest harus ada untuk satu form valid

---

## 10.3 ist_form_items
Pivot table yang menghubungkan `ist_forms` dengan `ist_subtests`, menyimpan konfigurasi spesifik per subtest dalam form.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| ist_form_id | char(36) | FK ist_forms.id |
| ist_subtest_id | char(36) | FK ist_subtests.id |
| is_randomized | boolean | default false, acak urutan soal |
| number_of_questions | int | default 100, jumlah soal yang ditampilkan |
| sort_order | int | default 0, urutan subtest dalam form |
| minimum_score | int | nullable, skor minimum kelulusan |
| multiplier | double | default 1, pengali skor (bobot) |
| duration_minutes | int | nullable, durasi per subtest dalam menit |
| clue_first | boolean | default false, tampilkan clue sebelum soal |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- Unique: `(ist_form_id, ist_subtest_id)`
- FK: `ist_form_id`, `ist_subtest_id`
- Index: `(ist_form_id, sort_order)`

**Business Rules:**
- Satu form dapat memiliki multiple items (normally 9 subtest)
- Kombinasi `ist_form_id` + `ist_subtest_id` harus unique
- `multiplier` digunakan untuk bobot skor (contoh: 1.5x untuk subtest tertentu)
- `clue_first` menampilkan petunjuk sebelum soal dimulai
- `number_of_questions` membatasi jumlah soal dari bank soal
- ON DELETE CASCADE dari kedua foreign key

---

## 10.4 ist_instructions
Instruksi untuk IST test form atau per subtest.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| ist_form_id | char(36) | nullable, FK ist_forms.id |
| ist_subtest_id | char(36) | nullable, FK ist_subtests.id |
| title | varchar | judul instruksi |
| content | text | konten instruksi (support HTML/Markdown) |
| sort_order | int | urutan tampilan instruksi |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `ist_form_id`, `ist_subtest_id`
- Index: `sort_order`

**Business Rules:**
- Either `ist_form_id` OR `ist_subtest_id` harus diisi (tidak boleh keduanya NULL)
- Jika `ist_form_id` diisi → instruksi untuk keseluruhan form
- Jika `ist_subtest_id` diisi → instruksi untuk subtest tertentu
- `sort_order` menentukan urutan tampilan (ascending)
- Support HTML/Markdown untuk formatting konten

---

## 10.5 ist_clues
Clue/hints untuk IST test yang dapat ditampilkan kepada user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| ist_form_id | char(36) | nullable, FK ist_forms.id |
| ist_subtest_id | char(36) | nullable, FK ist_subtests.id |
| clue | text | konten clue/hint |
| duration | int | nullable, detik sebelum clue muncul |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `ist_form_id`, `ist_subtest_id`

**Business Rules:**
- Either `ist_form_id` OR `ist_subtest_id` harus diisi
- `duration` = waktu setelah test dimulai sebelum clue tersedia
- Jika `duration` NULL, clue selalu tersedia
- Clue dapat berisi teks, gambar, atau multimedia

---

## 10.6 ist_subtest_questions
Mapping soal ke subtest IST.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| ist_subtest_id | char(36) | FK ist_subtests.id |
| question_id | char(36) | FK questions.id |
| sort_order | int | urutan soal |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `ist_subtest_id`, `question_id`
- Index: `sort_order`

---

## 10.7 ist_attempts
Record attempt IST test lengkap (9 subtest) oleh user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| test_attempt_id | char(36) | FK test_attempts.id |
| ist_form_id | char(36) | FK ist_forms.id |
| current_subtest_code | varchar | nullable, subtest yang sedang dikerjakan |
| attempt_number | int | nomor percobaan |
| status | enum | not_started/in_progress/completed/expired |
| started_at | datetime | nullable |
| submitted_at | datetime | nullable |
| deadline_at | datetime | nullable |
| total_score | decimal(10,2) | nullable, total skor keseluruhan |
| iq_score | int | nullable, skor IQ hasil konversi |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `test_attempt_id`, `ist_form_id`
- Index: `status`, `current_subtest_code`

**Business Rules:**
- `current_subtest_code` tracking subtest yang sedang dikerjakan
- `iq_score` dihitung dari konversi total skor 9 subtest ke skala IQ

---

## 10.8 ist_subtest_attempts
Record attempt per subtest IST (1-9).

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| ist_attempt_id | char(36) | FK ist_attempts.id |
| ist_subtest_id | char(36) | FK ist_subtests.id |
| subtest_code | varchar | kode subtest (SE, WA, AN, dll) |
| status | enum | not_started/in_progress/completed/skipped |
| started_at | datetime | nullable |
| submitted_at | datetime | nullable |
| deadline_at | datetime | nullable |
| raw_score | int | nullable, skor mentah |
| scaled_score | decimal(10,2) | nullable, skor yang sudah di-scale |
| random_seed | bigint | nullable, seed untuk randomisasi soal |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `ist_attempt_id`, `ist_subtest_id`
- Index: `subtest_code`, `status`

**Business Rules:**
- Subtest harus dikerjakan berurutan (tidak bisa skip ke subtest berikutnya)
- Setiap subtest punya durasi tersendiri
- `random_seed` digunakan untuk reproducible randomization soal

---

## 10.9 ist_answers
Jawaban per soal dalam subtest IST.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| ist_subtest_attempt_id | char(36) | FK ist_subtest_attempts.id |
| question_id | char(36) | FK questions.id |
| selected_option_id | char(36) | nullable, FK question_options.id |
| answer_text | longtext | nullable, jawaban text untuk essay |
| answer_json | json | nullable, jawaban dalam format JSON |
| is_correct | boolean | nullable |
| score | decimal(8,2) | nullable |
| answered_at | datetime | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `ist_subtest_attempt_id`, `question_id`, `selected_option_id`
- Index: `is_correct`, `answered_at`

---

## 10.10 ist_results
Hasil detail per kategori IST (Verbal, Numerical, Figural, Overall).

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| ist_attempt_id | char(36) | FK ist_attempts.id |
| category | varchar | verbal/numerical/figural/overall |
| raw_score | int | nullable, skor mentah kategori |
| scaled_score | decimal(10,2) | nullable, skor yang sudah di-scale |
| percentile | decimal(5,2) | nullable |
| interpretation | text | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `ist_attempt_id`
- Index: `category`

**Categories:**
- `verbal` — Kemampuan verbal (SE, WA, AN, GE)
- `numerical` — Kemampuan numerik (ME, RA, ZR)
- `figural` — Kemampuan figural (FA, WU)
- `overall` — Skor keseluruhan/IQ

**Scoring Formula:**
```
Verbal Score = (SE + WA + AN + GE) / 4
Numerical Score = (ME + RA + ZR) / 3
Figural Score = (FA + WU) / 2
IQ Score = f(Verbal, Numerical, Figural, age_norm)
```

---

# 11. Kraepelin Module

> **Disesuaikan dengan arsitektur `database-schema-employee-testing.md`.**
> Perubahan utama:
> - `kraepelin_forms` disederhanakan (config dipindah ke attempt level)
> - `kraepelin_form_columns` dan `kraepelin_form_numbers` **dihapus** dari config layer
> - Kolom dan angka sekarang di-generate pada saat attempt (execution layer)
> - Ditambahkan `kraepelin_attempt_columns` dan `kraepelin_attempt_numbers` di attempt level
> - `kraepelin_attempts` diperkaya dengan `numbers_per_column`, `columns_count`, `duration_per_column`, `total_skipped`

---

## 11.1 kraepelin_forms
Form/template untuk Kraepelin test.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| test_id | char(36) | FK tests.id |
| title | varchar | nama/judul form Kraepelin |
| description | text | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `test_id`

**Business Rules:**
- Kraepelin test menggunakan format kolom × baris angka
- Konfigurasi detail (jumlah kolom, angka, durasi) disimpan di level attempt
- Form bersifat template, angka di-generate saat test dimulai
- Output: skor kecepatan, akurasi, stabilitas

---

## 11.2 kraepelin_attempts
Record attempt Kraepelin test oleh user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| test_attempt_id | char(36) | FK test_attempts.id |
| kraepelin_form_id | char(36) | FK kraepelin_forms.id |
| numbers_per_column | int | jumlah angka per kolom |
| columns_count | int | jumlah kolom |
| duration_per_column | int | durasi per kolom (detik) |
| total_answered | int | default 0, total jawaban yang dijawab |
| total_correct | int | default 0, total jawaban benar |
| total_wrong | int | default 0, total jawaban salah |
| total_skipped | int | default 0, total yang dilewati |
| speed_score | decimal(10,2) | nullable |
| accuracy_score | decimal(10,2) | nullable |
| stability_score | decimal(10,2) | nullable |
| final_score | decimal(10,2) | nullable |
| attempt_number | int | nomor percobaan |
| status | enum | not_started/in_progress/submitted/expired |
| started_at | datetime | nullable |
| submitted_at | datetime | nullable |
| deadline_at | datetime | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `test_attempt_id`, `kraepelin_form_id`
- Index: `status`

**Business Rules:**
- `numbers_per_column`, `columns_count`, `duration_per_column` menyimpan konfigurasi yang digunakan saat attempt ini
- Konfigurasi disalin dari form template saat attempt dibuat, sehingga perubahan template tidak mempengaruhi attempt yang sedang berjalan
- `total_skipped` untuk tracking jawaban yang dilewati

---

## 11.3 kraepelin_attempt_columns
Kolom dalam Kraepelin test per attempt user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| kraepelin_attempt_id | char(36) | FK kraepelin_attempts.id |
| column_number | int | nomor kolom |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `kraepelin_attempt_id`
- Index: `column_number`

**Business Rules:**
- Kolom di-generate saat attempt dibuat
- Jumlah kolom sesuai `kraepelin_attempts.columns_count`

---

## 11.4 kraepelin_attempt_numbers
Angka-angka dalam kolom Kraepelin test per attempt user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| kraepelin_attempt_column_id | char(36) | FK kraepelin_attempt_columns.id |
| position | int | posisi angka dalam kolom |
| value | tinyint | nilai angka (0-9) |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `kraepelin_attempt_column_id`
- Index: `position`

**Business Rules:**
- Angka di-generate secara random saat attempt dibuat
- Jumlah angka per kolom sesuai `kraepelin_attempts.numbers_per_column`
- `value` berisi angka 0-9

---

## 11.5 kraepelin_answers
Jawaban Kraepelin test user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| kraepelin_attempt_id | char(36) | FK kraepelin_attempts.id |
| kraepelin_attempt_column_id | char(36) | FK kraepelin_attempt_columns.id |
| position | int | posisi jawaban dalam kolom |
| top_number | tinyint | angka atas |
| bottom_number | tinyint | angka bawah |
| user_answer | tinyint | nullable, jawaban user |
| correct_answer | tinyint | jawaban yang benar |
| is_correct | boolean | nullable |
| answered_at | datetime | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `kraepelin_attempt_id`, `kraepelin_attempt_column_id`
- Index: `position`, `is_correct`

---

# 12. Psychotest Module (pengganti Papikostick)

> **Disesuaikan dengan arsitektur `database-schema-employee-testing.md`.**
> Modul Papikostick sebelumnya direfactor menjadi **Psychotest Module** yang lebih generik.
> Perubahan utama:
> - `papikostick_forms` → `psychotest_forms`
> - `papikostick_dimensions` → `psychotest_aspects` + `psychotest_characteristics` (2 level hierarki)
> - Ditambahkan `psychotest_characteristic_scores` untuk score levels & interpretasi
> - `papikostick_items` → `psychotest_questions`
> - `papikostick_item_options` → `psychotest_question_options`
> - Ditambahkan `psychotest_option_characteristic_mappings` untuk weighted mapping
> - `papikostick_attempts` → `psychotest_attempts`
> - `papikostick_answers` → `psychotest_answers`
> - `papikostick_results` → `psychotest_result_characteristics` + `psychotest_result_aspects` (2 tabel hasil)
>
> Keuntungan:
> - Lebih generik dan extensible, bisa dipakai untuk Papikostick, PAPI Kostick, maupun psychotest lainnya
> - Hierarki aspects → characteristics memberikan granularity scoring yang lebih baik
> - Weighted mapping memungkinkan scoring yang lebih fleksibel

---

## 12.1 psychotest_aspects
Aspek-aspek yang diukur dalam psychotest (misalnya: Leadership, Work Direction, Activity, dll).

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| code | varchar | unique, kode aspek |
| name | varchar | nama aspek |
| description | text | nullable, deskripsi detail aspek |
| sort_order | int | urutan tampilan |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- Unique: `code`
- Index: `sort_order`

**Business Rules:**
- `code` harus unique (contoh: 'LDR' untuk Leadership, 'WD' untuk Work Direction)
- Aspek dapat memiliki multiple characteristics
- `sort_order` menentukan urutan tampilan dalam report

---

## 12.2 psychotest_characteristics
Karakteristik spesifik dalam setiap aspek psychotest.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| psychotest_aspect_id | char(36) | FK psychotest_aspects.id |
| code | varchar | kode karakteristik (unique per aspect) |
| name | varchar | nama karakteristik |
| description | text | nullable |
| sort_order | int | urutan dalam aspek |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `psychotest_aspect_id`
- Unique: `(psychotest_aspect_id, code)`
- Index: `sort_order`

**Business Rules:**
- `code` harus unique dalam satu aspek
- Setiap characteristic dapat memiliki multiple score levels
- Characteristic di-mapping ke question options melalui `psychotest_option_characteristic_mappings`

---

## 12.3 psychotest_characteristic_scores
Score levels dan interpretasi untuk setiap characteristic.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| psychotest_characteristic_id | char(36) | FK psychotest_characteristics.id |
| score | tinyint | nilai skor (0-10 atau sesuai skala) |
| description | text | interpretasi untuk score ini |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `psychotest_characteristic_id`
- Index: `(psychotest_characteristic_id, score)`

**Business Rules:**
- Setiap characteristic dapat memiliki multiple score levels
- Score description memberikan interpretasi kualitatif
- Digunakan untuk generate psychotest report

---

## 12.4 psychotest_forms
Form/template psychotest yang berisi kumpulan pertanyaan.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| test_id | char(36) | FK tests.id |
| name | varchar | nama form psychotest |
| description | text | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `test_id`

**Business Rules:**
- Satu form berisi multiple questions
- Form dapat digunakan untuk berbagai tipe psychotest (Papikostick, personality, aptitude, dll)
- Form dapat di-copy untuk modifikasi

---

## 12.5 psychotest_questions
Pertanyaan dalam psychotest form.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| psychotest_form_id | char(36) | FK psychotest_forms.id |
| number | int | nomor urut pertanyaan |
| is_active | boolean | default true |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `psychotest_form_id`
- Index: `is_active`, `number`

**Business Rules:**
- Pertanyaan harus memiliki minimal 2 options
- `number` menentukan urutan pertanyaan
- Hanya active questions yang ditampilkan dalam test

---

## 12.6 psychotest_question_options
Pilihan jawaban untuk pertanyaan psychotest.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| psychotest_question_id | char(36) | FK psychotest_questions.id |
| label | varchar | label option (A, B, C, dll) |
| statement | text | teks pernyataan option |
| sort_order | tinyint | urutan tampilan option |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `psychotest_question_id`
- Index: `sort_order`

**Business Rules:**
- Setiap option di-mapping ke characteristics via `psychotest_option_characteristic_mappings`
- `sort_order` menentukan urutan tampilan
- Umumnya psychotest menggunakan 4-7 options per pertanyaan

---

## 12.7 psychotest_option_characteristic_mappings
Mapping antara question options dengan characteristics dan bobotnya.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| psychotest_option_id | char(36) | FK psychotest_question_options.id |
| psychotest_aspect_id | char(36) | FK psychotest_aspects.id |
| psychotest_characteristic_id | char(36) | FK psychotest_characteristics.id |
| weight | tinyint | bobot kontribusi option terhadap characteristic |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `psychotest_option_id`, `psychotest_aspect_id`, `psychotest_characteristic_id`
- Index: `(psychotest_option_id, psychotest_characteristic_id)`

**Business Rules:**
- Satu option dapat di-mapping ke multiple characteristics
- `weight` menentukan seberapa besar kontribusi (typically 1-5 atau 1-10)
- Scoring calculation: sum of (weight × option_selected) per characteristic
- Core table untuk psychotest scoring algorithm

---

## 12.8 psychotest_attempts
Record attempt psychotest oleh user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| test_attempt_id | char(36) | FK test_attempts.id |
| psychotest_form_id | char(36) | FK psychotest_forms.id |
| attempt_number | int | nomor percobaan |
| status | enum | not_started/in_progress/submitted/expired |
| started_at | datetime | nullable |
| submitted_at | datetime | nullable |
| deadline_at | datetime | nullable |
| score | decimal(10,2) | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `test_attempt_id`, `psychotest_form_id`
- Index: `status`

---

## 12.9 psychotest_answers
Jawaban psychotest user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| psychotest_attempt_id | char(36) | FK psychotest_attempts.id |
| psychotest_question_id | char(36) | FK psychotest_questions.id |
| psychotest_option_id | char(36) | FK psychotest_question_options.id |
| answered_at | datetime | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `psychotest_attempt_id`, `psychotest_question_id`, `psychotest_option_id`
- Index: `answered_at`

---

## 12.10 psychotest_result_characteristics
Hasil skor per characteristic psychotest user.

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| psychotest_attempt_id | char(36) | FK psychotest_attempts.id |
| psychotest_characteristic_id | char(36) | FK psychotest_characteristics.id |
| raw_score | int | skor mentah |
| scaled_score | tinyint | skor yang sudah di-scale |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `psychotest_attempt_id`, `psychotest_characteristic_id`

---

## 12.11 psychotest_result_aspects
Hasil skor per aspect psychotest user (agregasi dari characteristics).

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID primary key |
| psychotest_attempt_id | char(36) | FK psychotest_attempts.id |
| psychotest_aspect_id | char(36) | FK psychotest_aspects.id |
| raw_score | float | skor mentah |
| scaled_score | float | skor yang sudah di-scale |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:**
- PK: `id`
- FK: `psychotest_attempt_id`, `psychotest_aspect_id`

---

# 13. Reporting & Support Tables

## 13.1 user_progress
| Column | Type |
|---|---|
| id | char(36) |
| user_id | char(36) |
| program_id | char(36) |
| test_type_id | char(36) nullable |
| total_attempts | int |
| average_score | decimal(10,2) nullable |
| last_attempt_at | datetime nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 13.2 bookmarks
| Column | Type |
|---|---|
| id | char(36) |
| user_id | char(36) |
| question_id | char(36) |
| created_at | timestamp |
| updated_at | timestamp |

## 13.3 activity_logs
| Column | Type |
|---|---|
| id | char(36) |
| user_id | char(36) nullable |
| action | varchar |
| subject_type | varchar nullable |
| subject_id | char(36) nullable |
| metadata | json nullable |
| ip_address | varchar nullable |
| user_agent | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

---

# 14. Relasi Utama

## Core
- users 1..1 user_profiles
- users 1..N subscriptions
- subscription_plans 1..N subscriptions
- programs 1..N tests
- test_types 1..N tests
- tests 1..N test_sections
- tests 1..N questions
- questions 1..N question_options
- questions 1..N question_essay_answers
- users 1..N test_attempts
- test_attempts 1..N attempt_answers
- test_attempts 1..1 test_results

## DISC Module
- tests 1..1 disc_forms
- disc_forms 1..N disc_questions
- disc_questions 1..N disc_options (exactly 4 per question)
- disc_options 1..N disc_option_scorings (2 per option: most & least)
- test_attempts 1..1 disc_attempts
- disc_attempts 1..N disc_answers
- disc_attempts 1..1 disc_results

## IST Module
- tests 1..1 ist_forms
- ist_forms 1..N ist_subtests (exactly 9 per form)
- ist_forms 1..N ist_form_items (pivot config)
- ist_forms 1..N ist_instructions
- ist_subtests 1..N ist_instructions
- ist_forms 1..N ist_clues
- ist_subtests 1..N ist_clues
- ist_subtests 1..N ist_subtest_questions
- test_attempts 1..1 ist_attempts
- ist_attempts 1..N ist_subtest_attempts
- ist_subtest_attempts 1..N ist_answers
- ist_attempts 1..N ist_results

## Kraepelin Module
- tests 1..1 kraepelin_forms
- test_attempts 1..1 kraepelin_attempts
- kraepelin_attempts 1..N kraepelin_attempt_columns
- kraepelin_attempt_columns 1..N kraepelin_attempt_numbers
- kraepelin_attempts 1..N kraepelin_answers

## Psychotest Module (pengganti Papikostick)
- psychotest_aspects 1..N psychotest_characteristics
- psychotest_characteristics 1..N psychotest_characteristic_scores
- tests 1..1 psychotest_forms
- psychotest_forms 1..N psychotest_questions
- psychotest_questions 1..N psychotest_question_options
- psychotest_question_options 1..N psychotest_option_characteristic_mappings
- test_attempts 1..1 psychotest_attempts
- psychotest_attempts 1..N psychotest_answers
- psychotest_attempts 1..N psychotest_result_characteristics
- psychotest_attempts 1..N psychotest_result_aspects

---

# 15. Catatan Desain

## 15.1 Mengapa Generic + Specialized?
Karena:
- CPNS cocok di engine generik
- DISC, IST, Kraepelin, Psychotest perlu logika scoring khusus
- ini membuat database tetap fleksibel dan maintainable

## 15.2 Mengapa Papikostick diubah menjadi Psychotest?
Karena:
- Arsitektur psychotest lebih generik dan extensible
- Bisa dipakai untuk Papikostick, PAPI Kostick, maupun tipe psychotest personality lainnya
- Hierarki **aspects → characteristics** memberikan granularity scoring yang lebih baik
- **Weighted mapping** memungkinkan scoring yang lebih fleksibel dibanding direct scoring
- Hasil terbagi 2 level: per **characteristic** dan per **aspect** (agregasi), memberikan insight lebih mendalam

## 15.3 Mengapa Kraepelin dipindah ke Attempt Level?
Karena:
- Angka Kraepelin sebaiknya di-generate secara random saat attempt dimulai
- Setiap attempt bisa memiliki set angka yang berbeda
- Konfigurasi (jumlah kolom, angka, durasi) dicatat di level attempt agar immutable
- Perubahan template form tidak mempengaruhi attempt yang sedang/sudah berjalan

## 15.4 Rekomendasi Naming
- gunakan UUID untuk entity bisnis utama
- gunakan snake_case konsisten
- enum bisa dibuat sebagai string di level aplikasi jika ingin fleksibel

## 15.5 Rekomendasi Soft Delete
Gunakan soft delete untuk tabel berikut:
- users
- subscription_plans
- test_packages
- tests
- questions

---
