# Implementation Plan SaaS Latihan Tes CPNS & BUMN

> Dokumen ini menjelaskan rencana implementasi platform SaaS latihan tes untuk peserta seleksi CPNS/PNS dan BUMN.
> Platform mendukung berbagai jenis test seperti TWK/TIU/TKP, DISC, IST, Kraepelin, dan Psychotest.
> Stack utama: Laravel, Inertia.js, Vue.js.
>
> **Version 3** — Disesuaikan dengan `database-schema-saas-cpns-bumn_Version3.md`
> Perubahan utama:
> - Papikostick diganti menjadi Psychotest Module
> - DISC, IST, Kraepelin Module diperbarui sesuai arsitektur baru

---

## 1. Product Vision

Membangun platform SaaS latihan tes yang dapat digunakan oleh peserta seleksi kerja/pemerintahan di Indonesia, khususnya:

- peserta **CPNS/PNS**
- peserta **BUMN**
- dan dapat diperluas untuk kebutuhan rekrutmen lain di masa depan

Platform harus memungkinkan pengguna untuk:
- registrasi dan login
- memilih jalur persiapan tes
- berlangganan paket latihan
- mengerjakan simulasi tes berdasarkan kategori
- melihat hasil, pembahasan, dan riwayat latihan

Platform juga harus menyediakan dashboard admin/super admin untuk:
- mengelola bank soal
- mengelola jenis tes
- mengelola paket subscription
- memantau user, transaksi, dan performa latihan

---

## 2. Target User & Segment

## 2.1 Segment Utama
- Peserta seleksi **CPNS/PNS**
- Peserta seleksi **BUMN**

## 2.2 Segment Tambahan
- Mahasiswa/fresh graduate yang ingin latihan psikotes kerja
- Peserta rekrutmen perusahaan swasta
- Lembaga pelatihan atau mentor persiapan tes

---

## 3. Scope Produk

## 3.1 Jalur Persiapan
Platform menyediakan beberapa jalur belajar:
- **CPNS/PNS Preparation**
- **BUMN Preparation**
- **General Psychotest Preparation**

## 3.2 Jenis Tes yang Didukung

### Untuk CPNS/PNS
- TWK
- TIU
- TKP
- mini tryout
- full tryout

### Untuk BUMN
- DISC
- IST
- Kraepelin
- Psychotest (pengganti Papikostick — lebih generik, mendukung berbagai tipe personality test)
- kemungkinan tambahan: personality test, verbal, numeric, logical reasoning

### Cross-program / reusable
- custom quiz/test
- timed practice
- full simulation package
- section-based exam

---

## 4. Core Product Principles

1. **Modular test engine**
   Setiap jenis tes harus diperlakukan sebagai modul terpisah tetapi tetap berada dalam satu platform.

2. **Subscription-based access**
   Akses ke paket premium dibuka berdasarkan langganan aktif.

3. **Program-aware content**
   Soal, simulasi, dan pembahasan diklasifikasikan berdasarkan jalur seperti CPNS atau BUMN.

4. **Scalable architecture**
   Sistem harus mudah ditambah jenis tes baru tanpa refactor besar.

5. **Analytics-first**
   Semua attempt, score, dan progress disimpan untuk analitik user dan admin.

---

## 5. Rekomendasi Arsitektur Sistem

## 5.1 Architecture Style
Gunakan arsitektur modular monolith dengan Laravel, sehingga:
- lebih cepat dikembangkan pada fase awal
- lebih mudah dikelola dibanding microservices
- tetap bisa dipisah per domain/module

## 5.2 Modul Utama Backend
- Auth & User Management
- Landing Page / CMS ringan
- Subscription & Billing
- Product / Program Management
- Test Catalog Management
- Question Bank Management
- Test Engine
- Attempt & Scoring
- Result & Analytics
- Admin Panel

## 5.3 Modul Frontend
- Public Landing Pages
- Auth Pages
- User Dashboard
- Test Taking Interface
- Result Pages
- Admin Dashboard

---

## 6. Role & Permission

## 6.1 Guest
- melihat landing page
- melihat paket berlangganan
- melihat preview produk
- registrasi/login

## 6.2 Registered User
- mengelola profil
- melihat program tes
- membeli paket
- mengakses materi/tes sesuai subscription
- mengerjakan latihan
- melihat riwayat hasil

## 6.3 Super Admin
- CRUD user
- CRUD subscription plan
- CRUD kategori tes
- CRUD soal dan pembahasan
- CRUD paket tryout
- melihat transaksi
- melihat analitik global
- mengatur publishing konten

## 6.4 Optional Future Roles
- content admin
- reviewer soal
- finance admin
- support/admin CS

---

## 7. Domain Model Tingkat Tinggi

Agar fleksibel untuk CPNS dan BUMN, struktur domain sebaiknya seperti ini:

### 7.1 Program
Contoh:
- CPNS
- BUMN
- General Psychotest

### 7.2 Test Type
Contoh:
- TWK
- TIU
- TKP
- DISC
- IST
- Kraepelin
- Psychotest

**engine_type:**
- generic
- disc
- ist
- kraepelin
- psychotest

### 7.3 Test Package
Contoh:
- Paket Latihan CPNS Basic
- Paket Tryout CPNS Premium
- Paket BUMN Psikotes
- Paket DISC + IST + Kraepelin
- Paket Full Simulation BUMN

### 7.4 Test / Exam
Satu unit latihan/simulasi yang dikerjakan user.

### 7.5 Question Bank
Bank soal per test type.

### 7.6 Attempt
Riwayat pengerjaan user.

### 7.7 Result
Hasil akhir, skor, interpretasi, dan pembahasan.

---

## 8. Modul Produk yang Direkomendasikan

## 8.1 Landing Page & Marketing
Fitur:
- halaman utama
- penjelasan platform
- penjelasan jalur CPNS & BUMN
- section paket harga
- testimonial
- FAQ
- CTA daftar

## 8.2 Authentication
Fitur:
- register
- login
- logout
- reset password
- verifikasi email
- social login (opsional)

## 8.3 User Dashboard
Fitur:
- overview akun
- status subscription
- progress belajar
- daftar tes tersedia
- riwayat pengerjaan
- rekomendasi tes berikutnya

## 8.4 Subscription & Payment
Fitur:
- daftar paket
- checkout
- pembayaran
- aktivasi akses
- invoice
- riwayat transaksi

## 8.5 Program Catalog
Fitur:
- daftar jalur (CPNS, BUMN, dll)
- daftar jenis tes per jalur
- daftar simulasi per kategori
- filter berdasarkan tingkat kesulitan

## 8.6 Test Engine
Fitur umum:
- start test
- timer
- question navigation
- autosave
- submit
- scoring
- pembahasan
- essay auto-grading (exact/fuzzy/contains/regex matching via question_essay_answers)

Fitur spesifik:
- **CPNS:** objective multiple choice, category-based scoring (TWK/TIU/TKP)
- **DISC:** forced-choice Most/Least UI, option scoring via disc_option_scorings (response_type + disc_code + score_value), most/least detail result
- **IST:** sequential 9-subtest flow, configurable via ist_form_items (randomisasi, multiplier, clue_first), instructions & clues display, per-subtest timer, random_seed for reproducible randomization
- **Kraepelin:** timed sequential arithmetic interface, config di attempt level (numbers_per_column, columns_count, duration_per_column), random number generation per attempt, speed/accuracy/stability scoring
- **Psychotest:** personality inventory dengan weighted scoring (aspects → characteristics → option mappings), 2-level result (per characteristic + per aspect aggregation), score levels interpretation

## 8.7 Result & Analytics
Fitur:
- skor per attempt
- grafik perkembangan
- hasil per kategori tes
- interpretasi psikotes
- history latihan
- strength/weakness insight
- **DISC:** most/least breakdown per dimensi, dominant profile, star score tracking
- **IST:** verbal/numerical/figural scores, IQ conversion, per-subtest scores
- **Kraepelin:** speed/accuracy/stability metrics, skipped tracking
- **Psychotest:** per-characteristic scores + scaled scores, per-aspect aggregation, interpretation from characteristic_scores table

## 8.8 Admin Panel
Fitur:
- dashboard statistik
- CRUD program
- CRUD test types
- CRUD test packages
- CRUD bank soal (termasuk essay answer keys)
- mapping soal ke paket/simulasi
- pengelolaan transaksi
- pengelolaan user
- pengelolaan banner/landing content
- **DISC Admin:** CRUD forms, questions, options, option_scorings
- **IST Admin:** CRUD forms, subtests, form_items config, instructions, clues, subtest_questions
- **Kraepelin Admin:** CRUD forms (template)
- **Psychotest Admin:** CRUD aspects, characteristics, characteristic_scores, forms, questions, question_options, option_characteristic_mappings

---

## 9. Test Engine Design Strategy

Karena platform menggabungkan CPNS dan BUMN, maka test engine dibagi menjadi 2 layer:

## 9.1 Generic Layer
Dipakai semua tes:
- tests
- test_sections
- questions
- question_options
- question_essay_answers
- test_attempts
- attempt_answers
- test_results

## 9.2 Specialized Layer
Dipakai tes tertentu yang punya kebutuhan khusus:

### CPNS Module
- multiple choice standard
- skor benar/salah / bobot per kategori (cpns_score_rules)
- pembahasan per soal
- blueprint TWK/TIU/TKP (cpns_test_blueprints)

### DISC Module
- forced-choice Most/Least format (4 options per question)
- scoring via disc_option_scorings (response_type: most/least, disc_code: D/I/S/C/star)
- hasil: most_d/i/s/c/star, least_d/i/s/c/star, score_d/i/s/c
- dominant profile calculation

### IST Module
- 9 subtest structure (SE, WA, AN, GE, ME, RA, ZR, FA, WU)
- configurable via ist_form_items (randomisasi, multiplier, clue_first, number_of_questions)
- instructions & clues per form/subtest
- sequential subtest flow
- per-subtest timer
- random_seed for reproducible randomization
- kategori verbal/numerical/figural + overall
- IQ score conversion

### Kraepelin Module
- template form sederhana (title, description)
- config di attempt level (numbers_per_column, columns_count, duration_per_column)
- kolom & angka di-generate random per attempt
- timing per kolom
- akurasi, speed, stability, skipped tracking

### Psychotest Module (pengganti Papikostick)
- hierarki: aspects → characteristics → characteristic_scores
- form → questions → question_options
- weighted mapping: option → aspect + characteristic + weight (psychotest_option_characteristic_mappings)
- scoring: sum of (weight × option_selected) per characteristic
- 2-level result: per characteristic (raw_score, scaled_score) + per aspect (aggregation)
- interpretation dari characteristic_scores table

---

## 10. Psychotest Integration Strategy

> **Sebelumnya: Papikostick Integration Strategy**
> Diperbarui menjadi Psychotest Module yang lebih generik.

Karena Psychotest (termasuk Papikostick, PAPI Kostick, dan personality test lainnya) memiliki logika scoring yang spesifik, modul ini menggunakan arsitektur terpisah.

Pendekatan:
- tabel master aspects (aspek-aspek yang diukur)
- tabel characteristics per aspect (breakdown detail)
- tabel characteristic_scores (score levels + interpretasi kualitatif)
- tabel form psychotest (template)
- tabel questions per form (nomor urut + is_active)
- tabel question_options (label, statement, sort_order)
- tabel option_characteristic_mappings (weighted mapping: option → aspect + characteristic + weight)
- tabel user attempts
- tabel user answers
- tabel result per characteristic (raw_score, scaled_score)
- tabel result per aspect (raw_score, scaled_score - agregasi)

Dengan begitu:
- lebih mudah maintain
- lebih akurat untuk scoring psikotes
- bisa dikembangkan untuk report psikologis sederhana hingga profesional
- extensible: bisa dipakai untuk Papikostick, PAPI Kostick, atau tipe psychotest personality lainnya
- hierarki aspects → characteristics memberikan granularity scoring yang lebih baik
- weighted mapping memungkinkan satu option mempengaruhi multiple characteristics dengan bobot berbeda

---

## 11. Usulan Struktur Data Tingkat Tinggi

Berikut domain entity setelah update scope ke Version 3:

### User & Access
- users
- roles
- permissions (via model_has_roles)
- user_profiles

### Billing
- subscription_plans
- subscriptions
- payments
- plan_entitlements

### Program & Product
- programs
- test_types (engine_type: generic/disc/ist/kraepelin/psychotest)
- program_test_types
- test_packages
- test_package_items

### Generic Test Engine
- tests
- test_sections
- questions
- question_options
- question_essay_answers
- test_attempts
- attempt_answers
- test_results

### CPNS Specific
- cpns_test_blueprints
- cpns_score_rules

### DISC Specific
- disc_forms
- disc_questions
- disc_options
- disc_option_scorings
- disc_attempts
- disc_answers
- disc_results

### IST Specific
- ist_forms
- ist_subtests
- ist_form_items
- ist_instructions
- ist_clues
- ist_subtest_questions
- ist_attempts
- ist_subtest_attempts
- ist_answers
- ist_results

### Kraepelin Specific
- kraepelin_forms
- kraepelin_attempts
- kraepelin_attempt_columns
- kraepelin_attempt_numbers
- kraepelin_answers

### Psychotest Specific (pengganti Papikostick)
- psychotest_aspects
- psychotest_characteristics
- psychotest_characteristic_scores
- psychotest_forms
- psychotest_questions
- psychotest_question_options
- psychotest_option_characteristic_mappings
- psychotest_attempts
- psychotest_answers
- psychotest_result_characteristics
- psychotest_result_aspects

### Supportive
- bookmarks
- user_progress
- activity_logs

---

## 12. MVP Recommendation

Agar tidak terlalu berat, MVP dibagi dalam 2 fase bisnis:

## Phase MVP-A
Fokus:
- landing page
- auth
- user dashboard
- subscription
- CPNS basic module (TWK/TIU/TKP)
- BUMN module untuk DISC + IST
- admin CRUD soal (generic + DISC + IST)
- result history

## Phase MVP-B
Tambahan:
- Kraepelin module (config di attempt level, random number generation)
- Psychotest module (aspects → characteristics → weighted mapping → 2-level result)
- full simulation package
- analytics improvement
- advanced report interpretation

Alasan:
- Kraepelin butuh UI yang sangat interaktif (timed arithmetic columns)
- Psychotest butuh setup hierarki yang cukup banyak (aspects → characteristics → score levels → mappings)
- lebih aman jika dimasukkan setelah fondasi generic test engine dan DISC/IST stabil

---

## 13. Fase Implementasi Detail

## Phase 1 - Project Foundation
- setup Laravel + Inertia + Vue
- setup auth
- setup role & permission
- setup base layout
- setup admin panel foundation (Filament recommended)
- setup database migrations untuk core tables

## Phase 2 - Core Business Modules
- programs
- test types (dengan engine_type)
- program_test_types
- packages & package_items
- subscription_plans & plan_entitlements
- subscriptions
- payments
- access control (subscription access guard)

## Phase 3 - Generic Test Engine
- tests
- test_sections
- questions
- question_options
- question_essay_answers (auto-grading: exact/fuzzy/contains/regex)
- test_attempts
- attempt_answers
- test_results
- scoring engine sederhana (standard + weighted)

## Phase 4 - CPNS Module
- cpns_test_blueprints (TWK/TIU/TKP structure)
- cpns_score_rules (correct/wrong/empty scores per category)
- pembahasan soal
- tryout simulation
- category-based scoring
- ranking/summary sederhana

## Phase 5 - BUMN Psychotest Module (DISC + IST)

### Phase 5a - DISC Module
- disc_forms, disc_questions, disc_options
- disc_option_scorings (response_type: most/least, disc_code: D/I/S/C/star, score_value)
- disc_attempts, disc_answers
- disc_results (most/least detail per dimensi + dominant_profile)
- Most/Least answer UI
- DISC scoring engine
- Admin CRUD DISC (forms, questions, options, option_scorings)

### Phase 5b - IST Module
- ist_forms, ist_subtests (subtest_code, subtest_name, max_score)
- ist_form_items (pivot config: is_randomized, number_of_questions, multiplier, clue_first, duration_minutes, minimum_score)
- ist_instructions (per form/subtest: title, content, sort_order)
- ist_clues (per form/subtest: clue, duration)
- ist_subtest_questions (mapping soal ke subtest)
- ist_attempts (current_subtest_code, attempt_number, total_score, iq_score)
- ist_subtest_attempts (subtest_code, status, raw_score, scaled_score, random_seed)
- ist_answers (selected_option_id, answer_text, answer_json, is_correct, score)
- ist_results (category: verbal/numerical/figural/overall, raw_score, scaled_score, percentile, interpretation)
- Sequential subtest flow UI
- Per-subtest timer
- IST scoring engine (per category + overall + IQ conversion)
- Admin CRUD IST (forms, subtests, form_items, instructions, clues, subtest_questions)

## Phase 6 - Advanced Psychotest Module (Kraepelin + Psychotest)

### Phase 6a - Kraepelin Module
- kraepelin_forms (template: title, description)
- kraepelin_attempts (config di attempt level: numbers_per_column, columns_count, duration_per_column, total_answered/correct/wrong/skipped, speed/accuracy/stability/final scores)
- kraepelin_attempt_columns (kolom di-generate per attempt)
- kraepelin_attempt_numbers (angka random per attempt, value 0-9)
- kraepelin_answers (top_number, bottom_number, user_answer, correct_answer, is_correct)
- Timed sequential arithmetic UI
- Random number generation engine
- Kraepelin scoring engine (speed, accuracy, stability)
- Admin CRUD Kraepelin forms

### Phase 6b - Psychotest Module (pengganti Papikostick)
- psychotest_aspects (code, name, description, sort_order)
- psychotest_characteristics (per aspect: code, name, description, sort_order)
- psychotest_characteristic_scores (score levels + interpretasi kualitatif)
- psychotest_forms (per test: name, description)
- psychotest_questions (per form: number, is_active)
- psychotest_question_options (per question: label, statement, sort_order)
- psychotest_option_characteristic_mappings (weighted: option → aspect + characteristic + weight)
- psychotest_attempts (attempt_number, status, deadline_at, score)
- psychotest_answers (option selection + answered_at)
- psychotest_result_characteristics (raw_score, scaled_score per characteristic)
- psychotest_result_aspects (raw_score, scaled_score per aspect - agregasi)
- Psychotest scoring engine: sum of (weight × option_selected) per characteristic
- Profile report generation
- Admin CRUD Psychotest (aspects, characteristics, score levels, forms, questions, options, mappings)

## Phase 7 - Platform Enhancement
- coupon/referral
- leaderboard
- notifications
- gamification
- affiliate/institution package
- advanced analytics comparison
- detailed psychological report
- graphical performance analysis

---

## 14. Billing & Access Rules

1. User harus login untuk membeli paket.
2. User hanya bisa mengakses tes yang termasuk dalam paket aktif.
3. Satu paket bisa membuka beberapa program dan test type (via plan_entitlements).
4. Paket dapat memiliki:
   - durasi akses (subscription duration_days)
   - limit attempt (plan_entitlements.limit_attempts)
   - akses pembahasan (access_type: discussion)
   - akses report premium (access_type: report)
5. Setelah subscription berakhir, akses premium ditutup.

---

## 15. Admin Workflow

### 15.1 Content Workflow
- admin membuat program
- admin membuat test type (dengan engine_type)
- admin membuat bank soal/form:
  - **Generic:** questions + question_options + question_essay_answers
  - **DISC:** disc_forms → disc_questions → disc_options → disc_option_scorings
  - **IST:** ist_forms → ist_subtests → ist_form_items → ist_instructions → ist_clues → ist_subtest_questions
  - **Kraepelin:** kraepelin_forms (template sederhana)
  - **Psychotest:** psychotest_aspects → psychotest_characteristics → psychotest_characteristic_scores → psychotest_forms → psychotest_questions → psychotest_question_options → psychotest_option_characteristic_mappings
- admin menyusun paket latihan (test_packages + test_package_items)
- admin publish paket

### 15.2 User Workflow
- user register
- user pilih paket
- user bayar
- sistem aktifkan subscription
- user mengerjakan latihan:
  - **CPNS:** objective MC → auto-score per category
  - **DISC:** Most/Least selection → scoring via disc_option_scorings → most/least detail result
  - **IST:** sequential 9 subtest → per-subtest timer → scoring per category → IQ conversion
  - **Kraepelin:** timed arithmetic → random numbers per attempt → speed/accuracy/stability scoring
  - **Psychotest:** option selection → weighted mapping → per-characteristic + per-aspect result
- sistem menyimpan hasil & progress

---

## 16. Technical Recommendations for Laravel Stack

## 16.1 Backend
- Laravel 12.x
- MySQL 8+ / PostgreSQL 16+ (PostgreSQL recommended per SAD)
- Redis untuk cache/queue
- Laravel Horizon jika memakai queue intensif

## 16.2 Frontend
- Inertia.js
- Vue 3
- Tailwind CSS
- shadcn-vue (per SAD recommendation)
- Pinia untuk state management jika diperlukan

## 16.3 Admin
- Inertia admin custom, atau
- Filament untuk percepat backoffice

Jika fokus cepat go-live, **Filament untuk admin panel** sangat direkomendasikan.

## 16.4 Scoring Services (Strategy Pattern)
Direkomendasikan menggunakan strategy pattern untuk scoring:
- `GenericScoringService` — standard objective scoring
- `CpnsScoringService` — category-based scoring (TWK/TIU/TKP) via cpns_score_rules
- `DiscScoringService` — Most/Least scoring via disc_option_scorings → most/least aggregation per disc_code
- `IstScoringService` — per-subtest scoring → category aggregation (verbal/numerical/figural) → IQ conversion, with multiplier support
- `KraepelinScoringService` — speed/accuracy/stability calculation from kraepelin_answers
- `PsychotestScoringService` — weighted mapping calculation: sum of (weight × option_selected) per characteristic → aspect aggregation

---

## 17. Risk & Complexity Notes

### Risiko Teknis
- scoring psikotes tidak bisa disamakan semua → solved dengan strategy pattern per engine_type
- Psychotest butuh desain scoring yang hati-hati (weighted mapping, aspects → characteristics hierarchy)
- Kraepelin butuh UI yang sangat interaktif (timed columns, random number generation)
- IST memerlukan sequential subtest flow dengan per-subtest timer → complexity di frontend
- randomisasi soal harus konsisten (random_seed di ist_subtest_attempts)
- result interpretation harus tervalidasi (psychotest_characteristic_scores)
- essay auto-grading accuracy depends on match_type configuration

### Risiko Produk
- scope terlalu besar jika semua tes dibuat sekaligus → phased delivery (MVP-A → MVP-B)
- user CPNS dan BUMN punya behavior berbeda → program-aware content
- pricing package harus dipisah dengan jelas → plan_entitlements per program/test_type

---

## 18. Rekomendasi Next Step

Urutan kerja yang disarankan:

1. ✅ finalisasi **implementation plan** (dokumen ini)
2. ✅ susun **database schema v3** (sudah diselesaikan)
3. desain **high-level ERD** berdasarkan database schema v3
4. buat **migration plan Laravel** dengan urutan:
   - core tables (users, roles, profiles)
   - billing tables (plans, subscriptions, payments, entitlements)
   - catalog tables (programs, test_types, packages)
   - generic engine tables (tests, sections, questions, options, essay_answers, attempts, answers, results)
   - CPNS tables (blueprints, score_rules)
   - DISC tables (forms, questions, options, option_scorings, attempts, answers, results)
   - IST tables (forms, subtests, form_items, instructions, clues, subtest_questions, attempts, subtest_attempts, answers, results)
   - Kraepelin tables (forms, attempts, attempt_columns, attempt_numbers, answers)
   - Psychotest tables (aspects, characteristics, characteristic_scores, forms, questions, question_options, option_characteristic_mappings, attempts, answers, result_characteristics, result_aspects)
   - support tables (bookmarks, user_progress, activity_logs)
5. buat **admin feature backlog** detail per modul
6. buat **UI sitemap** untuk user dan admin flows
7. buat **scoring service design** document (strategy pattern per engine_type)

---

## 19. Kesimpulan Arsitektur

Dengan tambahan kebutuhan BUMN, sistem diposisikan sebagai:
- **platform SaaS latihan seleksi CPNS, BUMN, dan psikotes kerja**

Arsitektur terbaik adalah:
- **modular monolith**
- **generic engine + specialized test modules**
- **subscription-based SaaS**
- **role-based admin management**

Dengan pendekatan ini, Anda bisa:
- mulai dari CPNS + BUMN (DISC + IST)
- lalu menambah Kraepelin dan Psychotest di phase berikutnya
- lalu menambah modul baru tanpa ubah struktur besar
- menjaga codebase tetap rapi untuk Laravel + Inertia + Vue

---

## 20. Deliverables Setelah Dokumen Ini

Setelah implementation plan ini, dokumen berikutnya yang sebaiknya dibuat:

1. ✅ `database-schema-saas-cpns-bumn_Version3.md` — sudah dibuat
2. ✅ `changelog-database-schema-v2-to-v3.md` — sudah dibuat
3. ✅ `feature-backlog-mvp_Version3.md` — sudah dibuat
4. `erd-saas-cpns-bumn.md` — high-level ERD
5. `laravel-migration-plan.md` — migration order dan script plan
6. `scoring-service-design.md` — strategy pattern per engine_type
7. `ui-sitemap.md` — user dan admin flow

---

## 21. Perubahan dari Version 2

| Aspek | V2 | V3 |
|---|---|---|
| Papikostick | Dedicated module | Diganti menjadi **Psychotest Module** (lebih generik) |
| DISC scoring | disc_dimension di disc_options | Scoring via **disc_option_scorings** (response_type + disc_code) |
| DISC results | score_d/i/s/c saja | Diperkaya: **most/least detail** per dimensi + star |
| IST config | Minimal | Ditambah: **ist_form_items** (pivot config), **ist_instructions**, **ist_clues** |
| IST answers | Via generic attempt_answers | Dedicated **ist_answers** tabel |
| IST attempts | Basic | Diperkaya: **iq_score**, **total_score** |
| IST subtest attempts | Basic | Diperkaya: **random_seed**, **status**, **deadline_at** |
| Kraepelin config | Config di form level | Config di **attempt level** (immutable per attempt) |
| Kraepelin columns/numbers | Di form level | Di **attempt level** (random per attempt) |
| Generic engine | Tanpa essay grading | Ditambah **question_essay_answers** (auto-grading) |
| Scoring services | Termasuk PapikostickScoringService | Diganti **PsychotestScoringService** |
| Test type engine_type | Termasuk papikostick | Diganti **psychotest** |
| Total tabel | ~31 specialized | ~41 specialized |