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
  - Papikostick
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
| DISC Module | form, question, scoring |
| IST Module | form, subtest, result |
| Kraepelin Module | form, column, answer |
| Papikostick Module | item, dimension, scoring |
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
- PAPKOSTICK

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
- papikostick

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

## 7.5 test_attempts
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

## 7.6 attempt_answers
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

## 7.7 test_results
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

## 9.1 disc_forms
| Column | Type |
|---|---|
| id | char(36) |
| test_id | char(36) |
| title | varchar |
| description | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 9.2 disc_questions
| Column | Type |
|---|---|
| id | char(36) |
| disc_form_id | char(36) |
| prompt | text nullable |
| number | int |
| created_at | timestamp |
| updated_at | timestamp |

## 9.3 disc_options
| Column | Type |
|---|---|
| id | char(36) |
| disc_question_id | char(36) |
| content | text |
| disc_dimension | enum |
| sort_order | int |
| created_at | timestamp |
| updated_at | timestamp |

**disc_dimension**
- D
- I
- S
- C

## 9.4 disc_attempts
| Column | Type |
|---|---|
| id | char(36) |
| test_attempt_id | char(36) |
| disc_form_id | char(36) |
| status | enum |
| started_at | datetime nullable |
| submitted_at | datetime nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 9.5 disc_answers
| Column | Type |
|---|---|
| id | char(36) |
| disc_attempt_id | char(36) |
| disc_question_id | char(36) |
| most_option_id | char(36) |
| least_option_id | char(36) |
| answered_at | datetime nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 9.6 disc_results
| Column | Type |
|---|---|
| id | char(36) |
| disc_attempt_id | char(36) |
| score_d | int |
| score_i | int |
| score_s | int |
| score_c | int |
| dominant_profile | varchar nullable |
| interpretation | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

---

# 10. IST Module

## 10.1 ist_forms
| Column | Type |
|---|---|
| id | char(36) |
| test_id | char(36) |
| name | varchar |
| description | text nullable |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

## 10.2 ist_subtests
| Column | Type |
|---|---|
| id | char(36) |
| ist_form_id | char(36) |
| code | enum |
| name | varchar |
| category | enum |
| duration_minutes | int nullable |
| sort_order | int |
| created_at | timestamp |
| updated_at | timestamp |

**code**
- SE
- WA
- AN
- GE
- ME
- RA
- ZR
- FA
- WU

**category**
- verbal
- numerical
- figural

## 10.3 ist_subtest_questions
| Column | Type |
|---|---|
| id | char(36) |
| ist_subtest_id | char(36) |
| question_id | char(36) |
| sort_order | int |
| created_at | timestamp |
| updated_at | timestamp |

## 10.4 ist_attempts
| Column | Type |
|---|---|
| id | char(36) |
| test_attempt_id | char(36) |
| ist_form_id | char(36) |
| current_subtest_code | varchar nullable |
| status | enum |
| started_at | datetime nullable |
| submitted_at | datetime nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 10.5 ist_subtest_attempts
| Column | Type |
|---|---|
| id | char(36) |
| ist_attempt_id | char(36) |
| ist_subtest_id | char(36) |
| started_at | datetime nullable |
| submitted_at | datetime nullable |
| raw_score | decimal(10,2) nullable |
| scaled_score | decimal(10,2) nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 10.6 ist_results
| Column | Type |
|---|---|
| id | char(36) |
| ist_attempt_id | char(36) |
| category | enum |
| raw_score | decimal(10,2) nullable |
| scaled_score | decimal(10,2) nullable |
| percentile | decimal(5,2) nullable |
| interpretation | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

---

# 11. Kraepelin Module

## 11.1 kraepelin_forms
| Column | Type |
|---|---|
| id | char(36) |
| test_id | char(36) |
| title | varchar |
| description | text nullable |
| columns_count | int |
| numbers_per_column | int |
| duration_per_column_seconds | int |
| created_at | timestamp |
| updated_at | timestamp |

## 11.2 kraepelin_form_columns
| Column | Type |
|---|---|
| id | char(36) |
| kraepelin_form_id | char(36) |
| column_number | int |
| created_at | timestamp |
| updated_at | timestamp |

## 11.3 kraepelin_form_numbers
| Column | Type |
|---|---|
| id | char(36) |
| kraepelin_form_column_id | char(36) |
| position | int |
| value | tinyint |
| created_at | timestamp |
| updated_at | timestamp |

## 11.4 kraepelin_attempts
| Column | Type |
|---|---|
| id | char(36) |
| test_attempt_id | char(36) |
| kraepelin_form_id | char(36) |
| status | enum |
| started_at | datetime nullable |
| submitted_at | datetime nullable |
| total_answered | int default 0 |
| total_correct | int default 0 |
| total_wrong | int default 0 |
| speed_score | decimal(10,2) nullable |
| accuracy_score | decimal(10,2) nullable |
| stability_score | decimal(10,2) nullable |
| final_score | decimal(10,2) nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 11.5 kraepelin_answers
| Column | Type |
|---|---|
| id | char(36) |
| kraepelin_attempt_id | char(36) |
| kraepelin_form_column_id | char(36) |
| position | int |
| top_number | tinyint |
| bottom_number | tinyint |
| user_answer | tinyint nullable |
| correct_answer | tinyint |
| is_correct | boolean nullable |
| answered_at | datetime nullable |
| created_at | timestamp |
| updated_at | timestamp |

---

# 12. Papikostick Module

## 12.1 papikostick_forms
| Column | Type |
|---|---|
| id | char(36) |
| test_id | char(36) |
| title | varchar |
| description | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 12.2 papikostick_dimensions
| Column | Type |
|---|---|
| id | char(36) |
| code | varchar |
| name | varchar |
| description | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 12.3 papikostick_items
| Column | Type |
|---|---|
| id | char(36) |
| papikostick_form_id | char(36) |
| item_no | int |
| statement | text |
| dimension_id | char(36) nullable |
| created_at | timestamp |
| updated_at | timestamp |

## 12.4 papikostick_item_options
| Column | Type |
|---|---|
| id | char(36) |
| papikostick_item_id | char(36) |
| label | varchar |
| score_value | int |
| sort_order | int |
| created_at | timestamp |
| updated_at | timestamp |

## 12.5 papikostick_attempts
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

## 12.6 papikostick_answers
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

## 12.7 papikostick_results
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
- users 1..N test_attempts
- test_attempts 1..N attempt_answers
- test_attempts 1..1 test_results

## Specialized
- tests 1..1 disc_forms
- tests 1..1 ist_forms
- tests 1..1 kraepelin_forms
- tests 1..1 papikostick_forms

---

# 15. Catatan Desain

## 15.1 Mengapa Generic + Specialized?
Karena:
- CPNS cocok di engine generik
- DISC, IST, Kraepelin, Papikostick perlu logika scoring khusus
- ini membuat database tetap fleksibel dan maintainable

## 15.2 Rekomendasi Naming
- gunakan UUID untuk entity bisnis utama
- gunakan snake_case konsisten
- enum bisa dibuat sebagai string di level aplikasi jika ingin fleksibel

## 15.3 Rekomendasi Soft Delete
Gunakan soft delete untuk tabel berikut:
- users
- subscription_plans
- test_packages
- tests
- questions

---

# 16. Next Step
Dokumen ini sebaiknya diikuti dengan:
- ERD high level
- migration plan Laravel
- backlog fitur MVP