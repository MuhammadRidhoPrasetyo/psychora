# Changelog Database Schema SaaS CPNS & BUMN

> Ringkasan perubahan dari **Version 2** ke **Version 3**
> Perubahan dilakukan agar modul DISC, IST, Kraepelin, dan Papikostick
> menyesuaikan arsitektur dari `database-schema-employee-testing.md`

---

# 1. Ikhtisar Perubahan

## 1.1 Scope Perubahan

| Section | Modul | Status |
|---|---|---|
| Section 1-6 | Core, Subscription, Program & Catalog | ✅ Tidak berubah |
| Section 7 | Generic Test Engine | ⚡ Ditambah `question_essay_answers` |
| Section 8 | CPNS Module | ✅ Tidak berubah |
| Section 9 | DISC Module | 🔄 Refactor signifikan |
| Section 10 | IST Module | 🔄 Refactor signifikan + tabel baru |
| Section 11 | Kraepelin Module | 🔄 Refactor arsitektur (config → attempt) |
| Section 12 | Papikostick → Psychotest Module | 🔄 Refactor total |
| Section 13 | Reporting & Support | ✅ Tidak berubah |
| Section 14 | Relasi Utama | 🔄 Diperbarui sesuai modul baru |
| Section 15 | Catatan Desain | 🔄 Ditambah penjelasan keputusan arsitektur |

## 1.2 Statistik Perubahan

| Aksi | Jumlah |
|---|---|
| Tabel baru ditambahkan | 11 |
| Tabel dihapus | 9 |
| Tabel direfactor/diperbarui | 12 |
| Tabel tidak berubah | Semua tabel Section 1-6, 8, 13 |

---

# 2. Perubahan Detail per Modul

---

## 2.1 Generic Test Engine (Section 7)

### Tabel Baru

#### `question_essay_answers` (Section 7.5)

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

**Alasan ditambahkan:**
- Referensi dari `database-schema-employee-testing.md` memiliki tabel ini
- Mendukung auto-grading pertanyaan essay
- Support multiple acceptable answers dengan bobot berbeda
- Mekanisme matching: exact, fuzzy (toleransi typo), contains (kata kunci), regex (pattern)

### Perubahan Nomor Section
| Lama | Baru | Tabel |
|---|---|---|
| 7.5 | 7.6 | test_attempts |
| 7.6 | 7.7 | attempt_answers |
| 7.7 | 7.8 | test_results |

---

## 2.2 DISC Module (Section 9)

### Perubahan Tabel Existing

#### `disc_forms` (9.1)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `title` | ✅ Ada | ❌ Dihapus, diganti `name` |
| Field `name` | ❌ Tidak ada | ✅ Ditambah |
| Indexes | Tidak didokumentasikan | ✅ Didokumentasikan lengkap |
| Business Rules | Tidak ada | ✅ Ditambah |

#### `disc_questions` (9.2)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `prompt` | ✅ Ada (text nullable) | ❌ **Dihapus** |
| Field `number` | ✅ Ada | ✅ Tetap ada |
| Field `disc_form_id` | ✅ Ada | ✅ Tetap ada |
| Indexes | Tidak didokumentasikan | ✅ Didokumentasikan |
| Business Rules | Tidak ada | ✅ Ditambah (exactly 4 options, Most/Least rules) |

**Alasan `prompt` dihapus:**
- Di `database-schema-employee-testing.md`, `disc_questions` hanya berisi `number`
- DISC test tidak memerlukan prompt text karena format sudah standar (pilih Most/Least dari 4 options)
- Prompt/instruksi cukup ditampilkan di level form

#### `disc_options` (9.3)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `content` | ✅ Ada (text) | ❌ **Dihapus**, diganti `option_text` |
| Field `option_text` | ❌ Tidak ada | ✅ **Ditambah** (text) |
| Field `disc_dimension` | ✅ Ada (enum D/I/S/C) | ❌ **Dihapus** |
| Field `sort_order` | ✅ Ada | ✅ Tetap ada |

**Alasan `disc_dimension` dihapus:**
- Scoring dimension dipindah ke tabel terpisah `disc_option_scorings`
- Satu option bisa memiliki scoring berbeda untuk Most vs Least response
- Lebih fleksibel dan akurat untuk kalkulasi DISC profile

#### `disc_attempts` (9.5)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `attempt_number` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `deadline_at` | ❌ Tidak ada | ✅ **Ditambah** (datetime nullable) |
| Field `score` | ❌ Tidak ada | ✅ **Ditambah** (decimal nullable) |
| Field `status` enum | draft/in_progress/submitted/expired | not_started/in_progress/submitted/expired |

#### `disc_answers` (9.6)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Struktur | Tidak berubah | Tidak berubah |
| Indexes | Tidak didokumentasikan | ✅ Didokumentasikan lengkap |

#### `disc_results` (9.7)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `score_d` | ✅ Ada (int) | ✅ Tetap ada, sekarang = most_d - least_d |
| Field `score_i` | ✅ Ada (int) | ✅ Tetap ada, sekarang = most_i - least_i |
| Field `score_s` | ✅ Ada (int) | ✅ Tetap ada, sekarang = most_s - least_s |
| Field `score_c` | ✅ Ada (int) | ✅ Tetap ada, sekarang = most_c - least_c |
| Field `most_d` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `most_i` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `most_s` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `most_c` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `most_star` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `least_d` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `least_i` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `least_s` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `least_c` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `least_star` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `dominant_profile` | ✅ Ada | ✅ Tetap ada |
| Field `interpretation` | ✅ Ada | ✅ Tetap ada |

### Tabel Baru

#### `disc_option_scorings` (9.4)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| disc_option_id | char(36) | FK disc_options.id |
| response_type | enum | most/least |
| disc_code | enum | D/I/S/C/star |
| score_value | int | nilai skor |
| created_at | timestamp | |
| updated_at | timestamp | |

**Alasan ditambahkan:**
- Di `database-schema-employee-testing.md` ada `disc_option_scorings`
- Memisahkan scoring logic dari option data
- Mendukung response_type (Most vs Least) dengan skor berbeda
- `disc_code` 'star' untuk neutral options
- Memungkinkan kalkulasi: Final_D = Most_D - Least_D

---

## 2.3 IST Module (Section 10)

### Perubahan Tabel Existing

#### `ist_forms` (10.1)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Struktur | Tidak berubah | Tidak berubah |
| Indexes | Tidak didokumentasikan | ✅ Didokumentasikan |
| Business Rules | Tidak ada | ✅ Ditambah |

#### `ist_subtests` (10.2)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Nama field `code` | ✅ Ada (enum) | ❌ **Diganti** → `subtest_code` (varchar) |
| Nama field `name` | ✅ Ada | ❌ **Diganti** → `subtest_name` |
| Field `category` | ✅ Ada (enum: verbal/numerical/figural) | ❌ **Dihapus** dari tabel (tetap di dokumentasi referensi) |
| Field `sort_order` | ✅ Ada | ✅ Tetap ada |
| Field `duration_minutes` | ✅ Ada | ✅ Tetap ada |
| Field `max_score` | ❌ Tidak ada | ✅ **Ditambah** (int nullable) |

**Alasan perubahan:**
- `subtest_code` sebagai varchar lebih fleksibel daripada enum (bisa ditambah tanpa migration)
- `category` dihapus dari tabel karena sudah fixed mapping (SE/WA/AN/GE=verbal, ME/RA/ZR=numerical, FA/WU=figural), cukup di level aplikasi
- `max_score` ditambah untuk konfigurasi skor maksimal per subtest

#### `ist_subtest_questions` (10.6)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Struktur | Tidak berubah | Tidak berubah |
| Nomor section | 10.3 | 10.6 |

#### `ist_attempts` (10.7)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `attempt_number` | ❌ Tidak ada | ✅ **Ditambah** |
| Field `deadline_at` | ❌ Tidak ada | ✅ **Ditambah** (datetime nullable) |
| Field `total_score` | ❌ Tidak ada | ✅ **Ditambah** (decimal nullable) |
| Field `iq_score` | ❌ Tidak ada | ✅ **Ditambah** (int nullable) |
| Field `status` enum | Tidak terdokumentasi | not_started/in_progress/completed/expired |
| Nomor section | 10.4 | 10.7 |

#### `ist_subtest_attempts` (10.8)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `subtest_code` | ❌ Tidak ada | ✅ **Ditambah** (varchar) |
| Field `status` | ❌ Tidak ada | ✅ **Ditambah** (enum) |
| Field `deadline_at` | ❌ Tidak ada | ✅ **Ditambah** (datetime nullable) |
| Field `random_seed` | ❌ Tidak ada | ✅ **Ditambah** (bigint nullable) |
| Field `raw_score` type | decimal(10,2) | **Diganti** → int |
| Nomor section | 10.5 | 10.8 |

**Alasan `random_seed` ditambah:**
- Dari `database-schema-employee-testing.md`
- Untuk reproducible randomization soal
- Seed yang sama menghasilkan urutan soal yang sama (untuk debugging/review)

#### `ist_results` (10.10)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `category` type | enum | **Diganti** → varchar |
| Field `raw_score` type | decimal(10,2) | **Diganti** → int |
| Scoring formula | Tidak ada | ✅ **Ditambah** |
| Nomor section | 10.6 | 10.10 |

### Tabel Baru

#### `ist_form_items` (10.3)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| ist_form_id | char(36) | FK ist_forms.id |
| ist_subtest_id | char(36) | FK ist_subtests.id |
| is_randomized | boolean | default false |
| number_of_questions | int | default 100 |
| sort_order | int | default 0 |
| minimum_score | int | nullable |
| multiplier | double | default 1 |
| duration_minutes | int | nullable |
| clue_first | boolean | default false |
| created_at | timestamp | |
| updated_at | timestamp | |

**Alasan ditambahkan:**
- Di `database-schema-employee-testing.md` ada `ist_test_form_items`
- Pivot table untuk konfigurasi per subtest dalam form
- `multiplier` untuk bobot skor berbeda per subtest
- `is_randomized` untuk randomisasi soal
- `clue_first` untuk tampilkan clue sebelum soal
- `number_of_questions` untuk membatasi jumlah soal dari bank

#### `ist_instructions` (10.4)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| ist_form_id | char(36) | nullable, FK ist_forms.id |
| ist_subtest_id | char(36) | nullable, FK ist_subtests.id |
| title | varchar | judul instruksi |
| content | text | konten (HTML/Markdown) |
| sort_order | int | urutan tampilan |
| created_at | timestamp | |
| updated_at | timestamp | |

**Alasan ditambahkan:**
- Di `database-schema-employee-testing.md` ada `ist_test_instructions`
- Menyimpan instruksi yang ditampilkan sebelum test dimulai
- Support instruksi per form keseluruhan ATAU per subtest tertentu
- Multiple instruksi dengan urutan tertentu

#### `ist_clues` (10.5)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| ist_form_id | char(36) | nullable, FK ist_forms.id |
| ist_subtest_id | char(36) | nullable, FK ist_subtests.id |
| clue | text | konten clue/hint |
| duration | int | nullable, detik sebelum muncul |
| created_at | timestamp | |
| updated_at | timestamp | |

**Alasan ditambahkan:**
- Di `database-schema-employee-testing.md` ada `ist_test_clues`
- Memberikan bantuan/petunjuk saat user kesulitan
- Clue bisa muncul setelah durasi tertentu (`duration`)
- Jika `duration` NULL, clue selalu tersedia

#### `ist_answers` (10.9)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| ist_subtest_attempt_id | char(36) | FK ist_subtest_attempts.id |
| question_id | char(36) | FK questions.id |
| selected_option_id | char(36) | nullable, FK question_options.id |
| answer_text | longtext | nullable |
| answer_json | json | nullable |
| is_correct | boolean | nullable |
| score | decimal(8,2) | nullable |
| answered_at | datetime | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Alasan ditambahkan:**
- Di `database-schema-employee-testing.md` ada `employee_ist_answers`
- Sebelumnya IST answers mungkin menggunakan generic `attempt_answers`
- Tabel khusus IST answers memberikan FK langsung ke `ist_subtest_attempts`
- Support multiple answer types: selected option, text, JSON

---

## 2.4 Kraepelin Module (Section 11)

### Perubahan Tabel Existing

#### `kraepelin_forms` (11.1)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `columns_count` | ✅ Ada | ❌ **Dihapus** (dipindah ke attempt) |
| Field `numbers_per_column` | ✅ Ada | ❌ **Dihapus** (dipindah ke attempt) |
| Field `duration_per_column_seconds` | ✅ Ada | ❌ **Dihapus** (dipindah ke attempt) |
| Sisa fields | `id`, `test_id`, `title`, `description`, timestamps | ✅ Tetap ada |

**Alasan disederhanakan:**
- Di `database-schema-employee-testing.md`, `kraepelin_test_forms` hanya berisi `title` dan `description`
- Konfigurasi detail disimpan di level attempt agar immutable
- Perubahan template tidak mempengaruhi attempt yang sedang/sudah berjalan
- Angka di-generate random saat attempt dimulai

#### `kraepelin_attempts` (11.2)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| Field `numbers_per_column` | ❌ Tidak ada | ✅ **Ditambah** (int) |
| Field `columns_count` | ❌ Tidak ada | ✅ **Ditambah** (int) |
| Field `duration_per_column` | ❌ Tidak ada | ✅ **Ditambah** (int, detik) |
| Field `total_skipped` | ❌ Tidak ada | ✅ **Ditambah** (int default 0) |
| Field `attempt_number` | ❌ Tidak ada | ✅ **Ditambah** (int) |
| Field `deadline_at` | ❌ Tidak ada | ✅ **Ditambah** (datetime nullable) |
| Field `status` enum | Tidak terdokumentasi | not_started/in_progress/submitted/expired |
| Nomor section | 11.4 | 11.2 |

#### `kraepelin_answers` (11.5)

| Aspek | V2 (Lama) | V3 (Baru) |
|---|---|---|
| FK `kraepelin_form_column_id` | ✅ Ada | ❌ **Diganti** → `kraepelin_attempt_column_id` |
| Sisa fields | Tidak berubah | Tidak berubah |
| Nomor section | 11.5 | 11.5 |

### Tabel Dihapus

#### `kraepelin_form_columns` (V2: 11.2)
**Status:** ❌ **DIHAPUS**

| Column | Type |
|---|---|
| id | char(36) |
| kraepelin_form_id | char(36) |
| column_number | int |
| created_at | timestamp |
| updated_at | timestamp |

**Alasan dihapus:**
- Kolom sekarang di-generate per attempt, bukan per form
- Digantikan oleh `kraepelin_attempt_columns`

#### `kraepelin_form_numbers` (V2: 11.3)
**Status:** ❌ **DIHAPUS**

| Column | Type |
|---|---|
| id | char(36) |
| kraepelin_form_column_id | char(36) |
| position | int |
| value | tinyint |
| created_at | timestamp |
| updated_at | timestamp |

**Alasan dihapus:**
- Angka sekarang di-generate random per attempt
- Digantikan oleh `kraepelin_attempt_numbers`

### Tabel Baru

#### `kraepelin_attempt_columns` (11.3)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| kraepelin_attempt_id | char(36) | FK kraepelin_attempts.id |
| column_number | int | nomor kolom |
| created_at | timestamp | |
| updated_at | timestamp | |

**Alasan ditambahkan:**
- Di `database-schema-employee-testing.md` ada `employee_kraepelin_columns`
- Kolom dibuat per attempt (bukan per form template)
- Setiap attempt bisa punya jumlah kolom berbeda

#### `kraepelin_attempt_numbers` (11.4)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| kraepelin_attempt_column_id | char(36) | FK kraepelin_attempt_columns.id |
| position | int | posisi angka dalam kolom |
| value | tinyint | nilai angka (0-9) |
| created_at | timestamp | |
| updated_at | timestamp | |

**Alasan ditambahkan:**
- Di `database-schema-employee-testing.md` ada `employee_kraepelin_numbers`
- Angka di-generate random saat attempt dibuat
- Setiap attempt mendapat set angka unik

---

## 2.5 Papikostick → Psychotest Module (Section 12)

> **Ini adalah perubahan paling besar.**
> Seluruh modul Papikostick (7 tabel) **dihapus** dan digantikan oleh modul Psychotest (11 tabel).

### Tabel Dihapus (seluruh modul Papikostick V2)

#### `papikostick_forms` (V2: 12.1)
**Status:** ❌ **DIHAPUS** → Digantikan `psychotest_forms`

| Column | Type |
|---|---|
| id | char(36) |
| test_id | char(36) |
| title | varchar |
| description | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

#### `papikostick_dimensions` (V2: 12.2)
**Status:** ❌ **DIHAPUS** → Digantikan `psychotest_aspects` + `psychotest_characteristics`

| Column | Type |
|---|---|
| id | char(36) |
| code | varchar |
| name | varchar |
| description | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

#### `papikostick_items` (V2: 12.3)
**Status:** ❌ **DIHAPUS** → Digantikan `psychotest_questions`

| Column | Type |
|---|---|
| id | char(36) |
| papikostick_form_id | char(36) |
| item_no | int |
| statement | text |
| dimension_id | char(36) nullable |
| created_at | timestamp |
| updated_at | timestamp |

#### `papikostick_item_options` (V2: 12.4)
**Status:** ❌ **DIHAPUS** → Digantikan `psychotest_question_options`

| Column | Type |
|---|---|
| id | char(36) |
| papikostick_item_id | char(36) |
| label | varchar |
| score_value | int |
| sort_order | int |
| created_at | timestamp |
| updated_at | timestamp |

#### `papikostick_attempts` (V2: 12.5)
**Status:** ❌ **DIHAPUS** → Digantikan `psychotest_attempts`

| Column | Type |
|---|---|
| id | char(36) |
| test_attempt_id | char(36) |
| papikostick_form_id | char(36) |
| status | enum |
| started_at | datetime nullable |
| submitted_at | datetime nullable |
| created_at | timestamp |
| updated_at | timestamp |

#### `papikostick_answers` (V2: 12.6)
**Status:** ❌ **DIHAPUS** → Digantikan `psychotest_answers`

| Column | Type |
|---|---|
| id | char(36) |
| papikostick_attempt_id | char(36) |
| papikostick_item_id | char(36) |
| selected_option_id | char(36) |
| score_value | int |
| answered_at | datetime nullable |
| created_at | timestamp |
| updated_at | timestamp |

#### `papikostick_results` (V2: 12.7)
**Status:** ❌ **DIHAPUS** → Digantikan `psychotest_result_characteristics` + `psychotest_result_aspects`

| Column | Type |
|---|---|
| id | char(36) |
| papikostick_attempt_id | char(36) |
| papikostick_dimension_id | char(36) |
| raw_score | decimal(10,2) |
| normalized_score | decimal(10,2) nullable |
| interpretation | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

### Tabel Baru (Psychotest Module)

#### `psychotest_aspects` (12.1)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| code | varchar | unique |
| name | varchar | |
| description | text | nullable |
| sort_order | int | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Menggantikan:** `papikostick_dimensions` (sebagian)
**Perbedaan:** Sekarang menjadi level hierarki atas (aspect → characteristics)

#### `psychotest_characteristics` (12.2)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| psychotest_aspect_id | char(36) | FK psychotest_aspects.id |
| code | varchar | unique per aspect |
| name | varchar | |
| description | text | nullable |
| sort_order | int | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Menggantikan:** `papikostick_dimensions` (sebagian)
**Perbedaan:** Level hierarki bawah, lebih granular. Satu aspect punya multiple characteristics

#### `psychotest_characteristic_scores` (12.3)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| psychotest_characteristic_id | char(36) | FK psychotest_characteristics.id |
| score | tinyint | nilai skor (0-10) |
| description | text | interpretasi |
| created_at | timestamp | |
| updated_at | timestamp | |

**Sepenuhnya baru:** Tidak ada padanan di V2
**Tujuan:** Score levels dan interpretasi kualitatif per characteristic

#### `psychotest_forms` (12.4)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| test_id | char(36) | FK tests.id |
| name | varchar | |
| description | text | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Menggantikan:** `papikostick_forms`
**Perbedaan:** Lebih generik, bisa dipakai untuk berbagai tipe psychotest

#### `psychotest_questions` (12.5)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| psychotest_form_id | char(36) | FK psychotest_forms.id |
| number | int | nomor urut |
| is_active | boolean | default true |
| created_at | timestamp | |
| updated_at | timestamp | |

**Menggantikan:** `papikostick_items`
**Perbedaan:**
- Tidak ada `statement` field (konten ada di level options)
- Tidak ada `dimension_id` (mapping dilakukan via `psychotest_option_characteristic_mappings`)
- Ditambah `is_active` untuk soft-toggle

#### `psychotest_question_options` (12.6)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| psychotest_question_id | char(36) | FK psychotest_questions.id |
| label | varchar | A, B, C, dll |
| statement | text | teks pernyataan |
| sort_order | tinyint | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Menggantikan:** `papikostick_item_options`
**Perbedaan:**
- `score_value` dihapus (scoring via mapping table)
- Ditambah `statement` untuk teks pernyataan per option

#### `psychotest_option_characteristic_mappings` (12.7)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| psychotest_option_id | char(36) | FK psychotest_question_options.id |
| psychotest_aspect_id | char(36) | FK psychotest_aspects.id |
| psychotest_characteristic_id | char(36) | FK psychotest_characteristics.id |
| weight | tinyint | bobot kontribusi |
| created_at | timestamp | |
| updated_at | timestamp | |

**Sepenuhnya baru:** Tidak ada padanan di V2
**Tujuan:**
- Satu option bisa affect multiple characteristics
- Weighted scoring (bukan fixed score_value)
- Core table untuk psychotest scoring algorithm
- Formula: sum of (weight × option_selected) per characteristic

#### `psychotest_attempts` (12.8)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| test_attempt_id | char(36) | FK test_attempts.id |
| psychotest_form_id | char(36) | FK psychotest_forms.id |
| attempt_number | int | |
| status | enum | not_started/in_progress/submitted/expired |
| started_at | datetime | nullable |
| submitted_at | datetime | nullable |
| deadline_at | datetime | nullable |
| score | decimal(10,2) | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Menggantikan:** `papikostick_attempts`
**Perbedaan:** Ditambah `attempt_number`, `deadline_at`, `score`

#### `psychotest_answers` (12.9)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| psychotest_attempt_id | char(36) | FK psychotest_attempts.id |
| psychotest_question_id | char(36) | FK psychotest_questions.id |
| psychotest_option_id | char(36) | FK psychotest_question_options.id |
| answered_at | datetime | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Menggantikan:** `papikostick_answers`
**Perbedaan:** Tidak ada `score_value` (scoring dihitung dari mapping table)

#### `psychotest_result_characteristics` (12.10)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| psychotest_attempt_id | char(36) | FK psychotest_attempts.id |
| psychotest_characteristic_id | char(36) | FK psychotest_characteristics.id |
| raw_score | int | |
| scaled_score | tinyint | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Menggantikan:** `papikostick_results` (sebagian)
**Perbedaan:** Hasil per characteristic (lebih granular)

#### `psychotest_result_aspects` (12.11)

| Column | Type | Notes |
|---|---|---|
| id | char(36) | UUID |
| psychotest_attempt_id | char(36) | FK psychotest_attempts.id |
| psychotest_aspect_id | char(36) | FK psychotest_aspects.id |
| raw_score | float | |
| scaled_score | float | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Menggantikan:** `papikostick_results` (sebagian)
**Perbedaan:** Hasil per aspect (agregasi dari characteristics)

---

# 3. Mapping Tabel Lama → Baru

## 3.1 Mapping Lengkap

| V2 (Lama) | V3 (Baru) | Status |
|---|---|---|
| disc_forms | disc_forms | 🔄 Updated (title → name) |
| disc_questions | disc_questions | 🔄 Updated (hapus prompt) |
| disc_options | disc_options | 🔄 Updated (hapus disc_dimension) |
| — | disc_option_scorings | ✅ New |
| disc_attempts | disc_attempts | 🔄 Updated |
| disc_answers | disc_answers | ✅ No change |
| disc_results | disc_results | 🔄 Updated (most/least detail) |
| ist_forms | ist_forms | ✅ No change |
| ist_subtests | ist_subtests | 🔄 Updated (code→subtest_code, hapus category) |
| — | ist_form_items | ✅ New |
| — | ist_instructions | ✅ New |
| — | ist_clues | ✅ New |
| ist_subtest_questions | ist_subtest_questions | ✅ No change |
| ist_attempts | ist_attempts | 🔄 Updated |
| ist_subtest_attempts | ist_subtest_attempts | 🔄 Updated |
| — | ist_answers | ✅ New |
| ist_results | ist_results | 🔄 Updated |
| kraepelin_forms | kraepelin_forms | 🔄 Updated (disederhanakan) |
| kraepelin_form_columns | — | ❌ Deleted |
| kraepelin_form_numbers | — | ❌ Deleted |
| kraepelin_attempts | kraepelin_attempts | 🔄 Updated |
| — | kraepelin_attempt_columns | ✅ New |
| — | kraepelin_attempt_numbers | ✅ New |
| kraepelin_answers | kraepelin_answers | 🔄 Updated (FK changed) |
| papikostick_forms | psychotest_forms | 🔄 Replaced |
| papikostick_dimensions | psychotest_aspects | 🔄 Replaced |
| — | psychotest_characteristics | ✅ New |
| — | psychotest_characteristic_scores | ✅ New |
| papikostick_items | psychotest_questions | 🔄 Replaced |
| papikostick_item_options | psychotest_question_options | 🔄 Replaced |
| — | psychotest_option_characteristic_mappings | ✅ New |
| papikostick_attempts | psychotest_attempts | 🔄 Replaced |
| papikostick_answers | psychotest_answers | 🔄 Replaced |
| papikostick_results | psychotest_result_characteristics | 🔄 Replaced (split) |
| — | psychotest_result_aspects | ✅ New (split) |

## 3.2 Jumlah Tabel per Modul

| Modul | V2 | V3 | Selisih |
|---|---|---|---|
| Generic Test Engine | 7 tabel | 8 tabel | +1 |
| DISC Module | 6 tabel | 7 tabel | +1 |
| IST Module | 6 tabel | 10 tabel | +4 |
| Kraepelin Module | 5 tabel | 5 tabel | 0 (tapi 2 dihapus, 2 ditambah) |
| Papikostick / Psychotest Module | 7 tabel | 11 tabel | +4 |
| **Total perubahan** | **31 tabel** | **41 tabel** | **+10** |

---

# 4. Perubahan Relasi (Section 14)

## 4.1 Relasi Ditambah

```
# DISC Module (Baru)
disc_options 1..N disc_option_scorings

# IST Module (Baru)
ist_forms 1..N ist_form_items
ist_forms 1..N ist_instructions
ist_subtests 1..N ist_instructions
ist_forms 1..N ist_clues
ist_subtests 1..N ist_clues
ist_subtest_attempts 1..N ist_answers

# Kraepelin Module (Baru)
kraepelin_attempts 1..N kraepelin_attempt_columns
kraepelin_attempt_columns 1..N kraepelin_attempt_numbers

# Psychotest Module (Seluruhnya baru)
psychotest_aspects 1..N psychotest_characteristics
psychotest_characteristics 1..N psychotest_characteristic_scores
tests 1..1 psychotest_forms
psychotest_forms 1..N psychotest_questions
psychotest_questions 1..N psychotest_question_options
psychotest_question_options 1..N psychotest_option_characteristic_mappings
test_attempts 1..1 psychotest_attempts
psychotest_attempts 1..N psychotest_answers
psychotest_attempts 1..N psychotest_result_characteristics
psychotest_attempts 1..N psychotest_result_aspects
```

## 4.2 Relasi Dihapus

```
# Kraepelin (Dihapus)
kraepelin_forms 1..N kraepelin_form_columns
kraepelin_form_columns 1..N kraepelin_form_numbers

# Papikostick (Seluruhnya dihapus)
tests 1..1 papikostick_forms
papikostick_forms 1..N papikostick_items
papikostick_items 1..N papikostick_item_options
test_attempts 1..1 papikostick_attempts
papikostick_attempts 1..N papikostick_answers
papikostick_attempts 1..N papikostick_results
```

## 4.3 Relasi Generic Test Engine (Ditambah)

```
# Baru
questions 1..N question_options
questions 1..N question_essay_answers
```

---

# 5. Perubahan Catatan Desain (Section 15)

## 5.1 Catatan Baru Ditambah

### 15.2 Mengapa Papikostick diubah menjadi Psychotest?
- Arsitektur psychotest lebih generik dan extensible
- Bisa dipakai untuk Papikostick, PAPI Kostick, maupun tipe psychotest personality lainnya
- Hierarki **aspects → characteristics** memberikan granularity scoring yang lebih baik
- **Weighted mapping** memungkinkan scoring yang lebih fleksibel dibanding direct scoring
- Hasil terbagi 2 level: per **characteristic** dan per **aspect** (agregasi)

### 15.3 Mengapa Kraepelin dipindah ke Attempt Level?
- Angka Kraepelin sebaiknya di-generate secara random saat attempt dimulai
- Setiap attempt bisa memiliki set angka yang berbeda
- Konfigurasi (jumlah kolom, angka, durasi) dicatat di level attempt agar immutable
- Perubahan template form tidak mempengaruhi attempt yang sedang/sudah berjalan

---

# 6. Dampak terhadap Dokumen Lain

## 6.1 `feature-backlog-mvp_Version2.md`
Perlu diperbarui:
- Epic H (DISC Module): sesuaikan dengan tabel `disc_option_scorings` baru
- Epic I (IST Module): tambah fitur `ist_form_items`, `ist_instructions`, `ist_clues`
- Tambah Epic baru untuk Psychotest Module (menggantikan referensi Papikostick)

## 6.2 `implementation-plan_Version2.md`
Perlu diperbarui:
- Phase 5 (BUMN Psychotest Module): ganti referensi Papikostick → Psychotest
- Phase 6 (Advanced Psychotest Module): sesuaikan scope Kraepelin

## 6.3 `software-architecture-document_Version9.md`
Perlu diperbarui:
- Section 9.2 Specialized Layer: ganti Papikostick → Psychotest
- Section 10.4 Scoring Engine: ganti `PapikostickScoringService` → `PsychotestScoringService`
- Section 7.2 Domain Modules: update Scoring Domain list

## 6.4 Migration Plan Laravel
Perlu dibuat baru dengan urutan:
1. Tabel konfigurasi Psychotest (aspects, characteristics, characteristic_scores)
2. Tabel form Psychotest (forms, questions, question_options, option_characteristic_mappings)
3. Tabel baru DISC (disc_option_scorings)
4. Tabel baru IST (ist_form_items, ist_instructions, ist_clues, ist_answers)
5. Tabel baru Kraepelin (kraepelin_attempt_columns, kraepelin_attempt_numbers)
6. Tabel execution Psychotest (psychotest_attempts)
7. Tabel result Psychotest (psychotest_answers, psychotest_result_characteristics, psychotest_result_aspects)
8. Drop tabel Papikostick lama (jika migrating dari V2)
9. Drop tabel Kraepelin lama (kraepelin_form_columns, kraepelin_form_numbers)

---

# 7. Migration Notes (V2 → V3)

## 7.1 Breaking Changes
⚠️ Perubahan berikut adalah **breaking changes** dan memerlukan data migration jika sudah ada data di V2:

1. **Papikostick → Psychotest**: Seluruh 7 tabel dihapus dan diganti 11 tabel baru. Memerlukan data transformation.
2. **Kraepelin form_columns/numbers → attempt_columns/numbers**: Data existing perlu dipindah ke struktur baru.
3. **DISC disc_dimension dihapus dari disc_options**: Perlu migrasi data dimension ke `disc_option_scorings`.
4. **IST code enum → subtest_code varchar**: Perlu update data type.

## 7.2 Non-Breaking Changes
✅ Perubahan berikut adalah **additive** dan tidak memerlukan data migration:

1. Tabel baru `question_essay_answers`
2. Tabel baru `ist_form_items`, `ist_instructions`, `ist_clues`, `ist_answers`
3. Field baru di `disc_attempts` (`attempt_number`, `deadline_at`, `score`)
4. Field baru di `ist_attempts` (`attempt_number`, `deadline_at`, `total_score`, `iq_score`)
5. Field baru di `ist_subtest_attempts` (`subtest_code`, `status`, `deadline_at`, `random_seed`)
6. Field baru di `disc_results` (most/least columns)

## 7.3 Recommended Migration Order
```
1. Create new config tables (psychotest_aspects, characteristics, characteristic_scores)
2. Create new form tables (psychotest_forms, questions, options, mappings)
3. Create new DISC table (disc_option_scorings)
4. Create new IST tables (ist_form_items, ist_instructions, ist_clues)
5. Alter existing tables (add new columns)
6. Create new execution tables (kraepelin_attempt_columns/numbers, ist_answers)
7. Create new result tables (psychotest_attempts, answers, results)
8. Migrate data from old tables to new tables
9. Drop old tables (papikostick_*, kraepelin_form_columns, kraepelin_form_numbers)
10. Drop removed columns (disc_options.disc_dimension, ist_subtests.category, etc.)
```

---

**End of Changelog**

> 📌 **Version**: 3.0.0
> 📅 **Date**: 2026-03-10
> 📝 **Sumber referensi**: `database-schema-employee-testing.md`