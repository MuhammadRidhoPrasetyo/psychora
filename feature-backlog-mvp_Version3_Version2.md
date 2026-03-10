# Feature Backlog MVP

> Backlog fitur MVP untuk SaaS latihan tes CPNS & BUMN.
> Prioritas dibagi menjadi:
> - P0 = wajib untuk launch awal
> - P1 = penting setelah core stabil
> - P2 = enhancement
>
> **Version 3** — Disesuaikan dengan `database-schema-saas-cpns-bumn_Version3.md`
> Perubahan utama:
> - Papikostick diganti menjadi Psychotest Module (lebih generik)
> - DISC Module disesuaikan (disc_option_scorings, most/least scoring detail)
> - IST Module diperkaya (ist_form_items, ist_instructions, ist_clues, ist_answers)
> - Kraepelin Module direfactor (config di attempt level)

---

# 1. MVP Goal

Membangun versi awal platform yang memungkinkan user:
- registrasi/login
- membeli paket
- mengakses latihan tes
- mengerjakan tes
- melihat hasil
- khusus MVP: support CPNS basic + BUMN basic psychotest

---

# 2. MVP Scope

## In Scope
- landing page
- auth
- user dashboard
- subscription
- payment manual/semi otomatis
- generic test engine
- CPNS module
- DISC module (dengan disc_option_scorings dan most/least scoring)
- IST module (dengan form_items config, instructions, clues)
- admin CRUD soal
- hasil test dasar

## Out of Scope Sementara
- mobile app native
- leaderboard kompleks
- AI recommendation
- affiliate/referral
- institution dashboard
- proctoring canggih
- full Psychotest report psikologi profesional (masuk Phase 2)
- Kraepelin module (masuk Phase 2)

---

# 3. Product Backlog

## Epic A - Public Website

### P0
- Landing page homepage
- Section program CPNS
- Section program BUMN
- Pricing page
- FAQ page
- CTA register/login

### P1
- Blog/artikel
- Testimoni
- Promo banner management

---

## Epic B - Authentication & Account

### P0
- Register user
- Login user
- Logout
- Forgot password
- Email verification
- Edit profile

### P1
- Google login
- Phone verification

---

## Epic C - Subscription & Billing

### P0
- List subscription plans
- Detail plan
- Checkout page
- Create order/invoice
- Payment confirmation
- Activate subscription manually/admin verified
- Show subscription status in dashboard

### P1
- Midtrans/Xendit integration
- Coupon/discount
- Auto renewal

---

## Epic D - User Dashboard

### P0
- Dashboard summary
- Active subscription widget
- Available tests list
- Attempt history
- Recent scores

### P1
- Progress chart
- Personalized recommendation

---

## Epic E - Program & Catalog

### P0
- Program listing: CPNS/BUMN
- Test type listing (termasuk engine_type: psychotest)
- Test package listing
- Free vs premium visibility

### P1
- Search/filter tests
- Difficulty filter

---

## Epic F - Generic Test Engine

### P0
- Start test
- Timer
- Question navigation
- Save answer
- Submit test
- Auto score objective questions
- Result summary
- Essay answer key matching (question_essay_answers: exact/fuzzy/contains/regex)

### P1
- Auto save per question
- Resume in-progress test
- Randomized questions
- Bookmark question

---

## Epic G - CPNS Module

### P0
- Support TWK
- Support TIU
- Support TKP
- Category-based scoring (cpns_test_blueprints)
- Score rules per category (cpns_score_rules: correct/wrong/empty scores)
- Explanation per question
- CPNS tryout result summary

### P1
- Passing grade simulation
- National-style ranking mockup
- Package bundling by CPNS level

---

## Epic H - DISC Module

> **Diperbarui sesuai database V3:**
> - `disc_options` tidak lagi menyimpan `disc_dimension`
> - Scoring melalui `disc_option_scorings` (response_type: most/least, disc_code: D/I/S/C/star)
> - `disc_results` menyimpan detail most/least per dimensi

### P0
- DISC form management (disc_forms: name, description)
- DISC question management (disc_questions: number-based, tanpa prompt)
- DISC option management (disc_options: option_text, sort_order)
- DISC option scoring configuration (disc_option_scorings: response_type most/least, disc_code D/I/S/C/star, score_value)
- Most/least answer UI (user pilih 1 Most dan 1 Least dari 4 options)
- Validation: tidak boleh pilih option yang sama untuk Most dan Least
- DISC scoring engine:
  - Hitung skor per disc_code berdasarkan response_type
  - most_d, most_i, most_s, most_c, most_star
  - least_d, least_i, least_s, least_c, least_star
  - score_d = most_d - least_d (dan seterusnya)
- DISC result summary (disc_results: most/least detail + dominant_profile)
- DISC attempt tracking (disc_attempts: attempt_number, status, deadline_at, score)

### P1
- Profile interpretation detail (interpretation text di disc_results)
- DISC chart visualization (radar/bar chart dari score_d/i/s/c)
- Star score analysis

### P2
- DISC profile comparison antar attempt
- Export DISC result ke PDF

---

## Epic I - IST Module

> **Diperbarui sesuai database V3:**
> - `ist_subtests` menggunakan `subtest_code` (varchar) dan `subtest_name`, plus `max_score`
> - Ditambah `ist_form_items` (pivot config: randomisasi, jumlah soal, multiplier, clue_first)
> - Ditambah `ist_instructions` (instruksi per form/subtest)
> - Ditambah `ist_clues` (clue/hints dengan timer duration)
> - `ist_answers` terpisah dari generic attempt_answers
> - `ist_attempts` diperkaya dengan `iq_score`
> - `ist_subtest_attempts` diperkaya dengan `random_seed`

### P0
- IST form management (ist_forms: name, description, is_active)
- 9 subtest configuration (ist_subtests: subtest_code, subtest_name, sort_order, duration_minutes, max_score)
- IST form items configuration (ist_form_items):
  - is_randomized (acak urutan soal)
  - number_of_questions (batas jumlah soal)
  - sort_order (urutan subtest dalam form)
  - minimum_score (skor minimum kelulusan)
  - multiplier (bobot skor)
  - duration_minutes (override durasi per subtest)
  - clue_first (tampilkan clue sebelum soal)
- IST instructions management (ist_instructions):
  - Instruksi per form keseluruhan ATAU per subtest tertentu
  - Title, content (HTML/Markdown), sort_order
  - Ditampilkan sebelum test/subtest dimulai
- IST clues management (ist_clues):
  - Clue per form ATAU per subtest
  - Clue text + duration (detik sebelum muncul)
  - Jika duration NULL, clue selalu tersedia
- Sequential subtest flow (harus dikerjakan berurutan 1-9)
- Subtest timer (per subtest dari ist_form_items.duration_minutes)
- IST question mapping (ist_subtest_questions: question_id ke subtest)
- IST answer recording (ist_answers: selected_option_id, answer_text, answer_json, is_correct, score)
- IST scoring per category:
  - Verbal = (SE + WA + AN + GE) / 4
  - Numerical = (ME + RA + ZR) / 3
  - Figural = (FA + WU) / 2
- IST result summary (ist_results: category, raw_score, scaled_score, percentile, interpretation)
- IST attempt tracking (ist_attempts: current_subtest_code, attempt_number, total_score, iq_score)
- IST subtest attempt tracking (ist_subtest_attempts: subtest_code, status, raw_score, scaled_score, random_seed)

### P1
- Randomized subtest questions (menggunakan random_seed di ist_subtest_attempts)
- Detailed interpretation per category
- IQ score conversion (ist_attempts.iq_score dari fungsi age_norm)
- Multiplier-based scoring (ist_form_items.multiplier)
- Minimum score validation per subtest

### P2
- IST score trend across attempts
- Subtest performance comparison chart
- Export IST result ke PDF

---

## Epic J - Admin Panel

### P0
- Admin login
- Manage users
- Manage plans
- Manage programs
- Manage test types (termasuk engine_type: psychotest)
- Manage tests
- Manage sections
- Manage questions (generic + essay answer keys)
- Manage question options
- Manage question essay answers (answer_text, score, match_type, priority)
- Publish/unpublish tests
- View user attempts
- View payments
- **DISC Admin:**
  - CRUD disc_forms
  - CRUD disc_questions (number-based)
  - CRUD disc_options (option_text, sort_order)
  - CRUD disc_option_scorings (response_type, disc_code, score_value)
  - View disc_results
- **IST Admin:**
  - CRUD ist_forms
  - CRUD ist_subtests (subtest_code, subtest_name, max_score)
  - CRUD ist_form_items (config per subtest dalam form)
  - CRUD ist_instructions (per form/subtest)
  - CRUD ist_clues (per form/subtest)
  - CRUD ist_subtest_questions (mapping soal ke subtest)
  - View ist_results
  - View ist_answers

### P1
- Dashboard analytics
- Bulk import question bank
- Export results
- Bulk import DISC options + scorings
- Bulk import IST subtest questions

---

## Epic K - Reporting

### P0
- User result history
- Result detail page (generic + DISC + IST)
- Admin result viewer
- DISC result detail (most/least breakdown, dominant profile)
- IST result detail (per category: verbal/numerical/figural/overall, per subtest)

### P1
- Score trend chart
- Compare attempts
- Export PDF
- IST IQ score display
- DISC profile chart

---

## Epic L - Security & Logging

### P0
- Auth middleware
- Subscription access guard
- Attempt access validation
- Basic activity logs

### P1
- IP/device logging
- Suspicious behavior logs
- Rate limiting test start

---

# 4. Sprint Recommendation

## Sprint 1
- auth
- landing page
- core database (termasuk question_essay_answers)
- admin foundation

## Sprint 2
- subscription plans
- payments basic
- dashboard user
- catalog tests

## Sprint 3
- generic test engine
- questions/options
- essay answer key matching
- scoring objective

## Sprint 4
- CPNS module
- result page
- explanation page

## Sprint 5
- DISC module:
  - disc_forms, disc_questions, disc_options
  - disc_option_scorings (most/least, D/I/S/C/star)
  - disc_attempts, disc_answers
  - disc_results (most/least detail)
  - admin CRUD DISC
  - Most/Least answer UI
  - DISC scoring engine

## Sprint 6
- IST module:
  - ist_forms, ist_subtests (subtest_code, max_score)
  - ist_form_items (config pivot)
  - ist_instructions, ist_clues
  - ist_subtest_questions
  - ist_attempts, ist_subtest_attempts (random_seed)
  - ist_answers
  - ist_results (per category + overall)
  - Sequential subtest flow
  - Subtest timer
  - IST scoring engine
  - Admin CRUD IST

## Sprint 7
- logs
- reporting basic (generic + DISC + IST result details)
- polish UI/UX

---

# 5. Definition of Done MVP

MVP dianggap selesai jika:
- user dapat registrasi dan login
- user dapat membeli minimal 1 paket
- admin dapat mengaktifkan subscription
- user dapat mengakses latihan sesuai entitlement
- user dapat mengerjakan CPNS test (TWK/TIU/TKP)
- user dapat mengerjakan DISC test dengan Most/Least UI
- DISC scoring menghasilkan most/least detail dan dominant profile
- user dapat mengerjakan IST test dengan sequential 9 subtest
- IST scoring per category (verbal/numerical/figural) dan overall berfungsi
- IST instructions dan clues tampil dengan benar
- skor dan hasil tampil dengan benar untuk semua tipe test
- admin dapat mengelola bank soal dasar (generic + DISC + IST)
- admin dapat mengkonfigurasi DISC option scorings
- admin dapat mengkonfigurasi IST form items (randomisasi, multiplier, duration, clue_first)
- essay answer key matching berfungsi (exact/fuzzy/contains/regex)

---

# 6. Post-MVP Roadmap

## Phase 2
- Kraepelin module:
  - kraepelin_forms (template sederhana)
  - kraepelin_attempts (config di attempt level: numbers_per_column, columns_count, duration_per_column)
  - kraepelin_attempt_columns (kolom di-generate per attempt)
  - kraepelin_attempt_numbers (angka random per attempt)
  - kraepelin_answers (top_number, bottom_number, user_answer, correct_answer, is_correct)
  - Scoring: speed_score, accuracy_score, stability_score, total_skipped
  - Timed sequential arithmetic UI
- Psychotest module (pengganti Papikostick):
  - psychotest_aspects (hierarki atas: code, name, sort_order)
  - psychotest_characteristics (hierarki bawah per aspect: code, name, sort_order)
  - psychotest_characteristic_scores (score levels + interpretasi kualitatif)
  - psychotest_forms (template per test)
  - psychotest_questions (number, is_active)
  - psychotest_question_options (label, statement, sort_order)
  - psychotest_option_characteristic_mappings (weighted mapping: option → aspect + characteristic + weight)
  - psychotest_attempts (attempt_number, status, deadline_at, score)
  - psychotest_answers (option selection + answered_at)
  - psychotest_result_characteristics (raw_score, scaled_score per characteristic)
  - psychotest_result_aspects (raw_score, scaled_score per aspect - agregasi)
  - Scoring engine: sum of (weight × option_selected) per characteristic
  - Report: interpretasi per characteristic + per aspect
- Payment gateway full automation
- Analytics lebih detail

## Phase 3
- gamification
- leaderboard
- referral
- institution package
- mobile responsiveness improvement
- advanced reporting
- Psychotest: detailed psychological report
- Kraepelin: graphical performance analysis
- Cross-test comparison and insights

---

# 7. Notes

## Rekomendasi MVP bisnis
Jika ingin cepat validasi pasar:
- launch dulu dengan **CPNS + DISC + IST**
- Kraepelin dan Psychotest masuk gelombang berikutnya

## Alasan
- lebih cepat release
- effort UI/scoring lebih rendah untuk DISC dan IST
- Kraepelin butuh UI yang sangat interaktif (timed arithmetic columns)
- Psychotest (pengganti Papikostick) butuh setup aspects → characteristics → score levels yang cukup banyak
- bisa mulai mendapatkan feedback user lebih awal

## Perubahan dari Version 2
- **Papikostick** diganti terminologi menjadi **Psychotest** (lebih generik, bisa dipakai untuk berbagai tipe psychotest)
- **DISC** diperkaya: option scoring terpisah (disc_option_scorings), hasil detail most/least per dimensi
- **IST** diperkaya: form_items config, instructions, clues, dedicated ist_answers, iq_score, random_seed
- **Kraepelin** direfactor: config di attempt level, kolom/angka di-generate per attempt
- **Generic Test Engine** ditambah: question_essay_answers untuk auto-grading essay