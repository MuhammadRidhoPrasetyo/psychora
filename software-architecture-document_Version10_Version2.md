# Software Architecture Document (SAD)
## SaaS Platform Latihan Tes CPNS & BUMN

**Version:** 2.0.0
**Date:** 2026-03-10
**Stack:** Laravel + Inertia.js + Vue 3 + shadcn-vue + PostgreSQL

> **Changelog dari Version 1.0.0:**
> - Papikostick diganti menjadi Psychotest Module (lebih generik)
> - DISC Module disesuaikan (disc_option_scorings, most/least scoring detail)
> - IST Module diperkaya (ist_form_items, ist_instructions, ist_clues, ist_answers)
> - Kraepelin Module direfactor (config di attempt level)
> - Generic Test Engine ditambah question_essay_answers
> - Scoring Engine diperbarui (PapikostickScoringService → PsychotestScoringService)
> - Data Flow Scenarios diperkaya per modul specialized
> - Database Architecture disesuaikan dengan database-schema V3

---

# 1. Introduction

## 1.1 Purpose
Dokumen ini menjelaskan arsitektur perangkat lunak untuk platform SaaS latihan tes CPNS & BUMN yang akan dibangun menggunakan:

- **Laravel** sebagai backend framework
- **Inertia.js** sebagai bridge antara backend dan frontend
- **Vue 3** sebagai UI framework
- **shadcn-vue** sebagai UI component system
- **PostgreSQL** sebagai database utama

Dokumen ini menjadi acuan untuk:
- pengembangan sistem
- penyelarasan tim developer
- pengambilan keputusan arsitektur
- pengembangan fitur baru
- maintainability dan scalability sistem

---

## 1.2 Scope
Sistem mencakup:
- landing page
- autentikasi user
- dashboard user
- subscription dan pembayaran
- modul latihan CPNS (TWK/TIU/TKP)
- modul psikotes BUMN (DISC, IST, Kraepelin, Psychotest)
- admin panel untuk manajemen konten, tes, soal, dan user
- engine pengerjaan test dengan specialized scoring per tipe
- scoring, reporting, dan analytics

---

## 1.3 Business Context
Platform ini adalah SaaS berbasis langganan yang memungkinkan user mempersiapkan diri untuk:
- seleksi **CPNS/PNS**
- seleksi **BUMN**
- psikotes kerja umum

Jenis test yang didukung:
- TWK
- TIU
- TKP
- DISC
- IST
- Kraepelin
- Psychotest (pengganti Papikostick — lebih generik, mendukung berbagai tipe personality test)

---

# 2. Architectural Goals

## 2.1 Primary Goals
Arsitektur harus memenuhi tujuan berikut:

1. **Modular**
   - test engine harus bisa mendukung banyak jenis tes
   - modul baru bisa ditambahkan tanpa refactor besar
   - setiap tipe test memiliki scoring service tersendiri (strategy pattern)

2. **Maintainable**
   - kode mudah dipahami dan dikelola
   - domain terpisah dengan jelas
   - generic engine dan specialized modules terpisah secara arsitektur

3. **Scalable**
   - mampu menangani pertumbuhan user, soal, dan attempt
   - siap untuk scaling vertikal/horizontal bertahap
   - config test immutable per attempt (perubahan template tidak mempengaruhi attempt berjalan)

4. **Secure**
   - proteksi akses user
   - validasi subscription via entitlement
   - keamanan auth, payment, dan result data
   - randomisasi soal dengan reproducible seed

5. **Fast Development**
   - mendukung iterasi cepat untuk MVP dan fase lanjutan
   - phased delivery: CPNS + DISC + IST dulu, Kraepelin + Psychotest menyusul

---

## 2.2 Non-Functional Goals
- response time cepat untuk dashboard dan test interface
- auto-save jawaban stabil
- integritas hasil test terjaga
- deployment mudah
- observability dasar tersedia
- per-subtest timer accuracy (IST)
- timed column transition smoothness (Kraepelin)
- weighted scoring calculation precision (Psychotest)

---

# 3. System Context

## 3.1 Actors
### Guest
- melihat landing page
- melihat daftar paket
- registrasi / login

### Registered User
- membeli paket
- mengakses latihan
- mengerjakan tes (CPNS, DISC, IST, Kraepelin, Psychotest)
- melihat hasil

### Admin / Super Admin
- mengelola user
- mengelola paket
- mengelola test
- mengelola bank soal (generic + DISC + IST + Kraepelin + Psychotest)
- mengkonfigurasi scoring rules
- melihat transaksi
- melihat laporan

### External Systems
- Payment Gateway
- Email Service / SMTP
- Cache / Queue system
- Monitoring/logging tools

---

## 3.2 Context Diagram

```text
+------------------+
|      Guest       |
+------------------+
         |
         v
+-------------------------------+
| SaaS Latihan Tes CPNS & BUMN |
|-------------------------------|
| Modules:                     |
|  - Generic Test Engine       |
|  - CPNS (TWK/TIU/TKP)       |
|  - DISC                      |
|  - IST (9 subtests)          |
|  - Kraepelin                 |
|  - Psychotest                |
+-------------------------------+
   |           |           |
   v           v           v
 User       Admin     Payment Gateway
   |                       |
   v                       v
 Email Service         PostgreSQL
```

---

# 4. Architectural Style

## 4.1 Chosen Style
Arsitektur yang dipilih adalah:

**Modular Monolith**

## 4.2 Reasoning
Dipilih karena:
- lebih cepat dikembangkan untuk MVP
- lebih sederhana daripada microservices
- cocok untuk tim kecil/menengah
- tetap dapat dibagi berdasarkan domain/module
- mudah dipisah ke service terpisah jika sistem berkembang besar

---

## 4.3 Architectural Pattern
Kombinasi pola yang digunakan:
- **MVC** untuk sisi Laravel
- **Server-driven SPA** dengan Inertia.js
- **Domain-oriented module separation**
- **Service layer** untuk business logic
- **Strategy pattern** untuk scoring engine per tipe test
- **Repository optional** jika kompleksitas query meningkat
- **Event-driven async jobs** untuk proses tertentu

---

# 5. Technology Stack

## 5.1 Backend
- Laravel 12.x
- PHP 8.3+
- Laravel Queue
- Laravel Scheduler
- Laravel Policies / Gates
- Laravel Events & Jobs

## 5.2 Frontend
- Vue 3
- Inertia.js
- Vite
- TypeScript (direkomendasikan)
- shadcn-vue
- Tailwind CSS
- VueUse (opsional)
- Pinia (opsional, jika state makin kompleks — direkomendasikan untuk IST subtest flow dan Kraepelin timed columns)

## 5.3 Database
- PostgreSQL 16+

## 5.4 Infrastructure
- Docker / Docker Compose
- Nginx
- Redis
- Mailpit (development)
- CI/CD pipeline (GitHub Actions/GitLab CI)

---

# 6. High-Level Architecture

## 6.1 Logical Layers

```text
+------------------------------------------------------+
| Presentation Layer                                   |
| Inertia Pages, Vue Components, shadcn-vue UI         |
| - Test UIs: MC, Most/Least, Sequential Subtest,     |
|   Timed Arithmetic, Personality Inventory            |
+------------------------------------------------------+
| Application Layer                                    |
| Controllers, Form Requests, Actions, Services        |
+------------------------------------------------------+
| Domain Layer                                         |
| Modules: Auth, Billing, Test Engine, Results, Admin  |
| Scoring: Generic, CPNS, DISC, IST, Kraepelin,       |
|          Psychotest                                  |
+------------------------------------------------------+
| Infrastructure Layer                                 |
| PostgreSQL, Redis, Queue, Mail, File Storage         |
+------------------------------------------------------+
```

---

## 6.2 Module Overview

### Public Website Module
- landing page
- pricing page
- FAQ
- blog/content pages (optional)

### Authentication Module
- register/login/logout
- email verification
- password reset
- role-based access

### Subscription Module
- plan management
- order creation
- payment processing
- access entitlement (via plan_entitlements per program/test_type)

### Catalog Module
- programs
- test types (dengan engine_type: generic/disc/ist/kraepelin/psychotest)
- packages
- list available tests

### Test Engine Module
- start attempt
- question rendering
- answer submission
- auto-save
- timer (global + per-subtest untuk IST + per-column untuk Kraepelin)
- scoring (via strategy pattern per engine_type)

### Specialized Test Modules

#### CPNS Module
- TWK/TIU/TKP blueprint configuration
- category-based scoring rules (correct/wrong/empty)
- explanation per question

#### DISC Module
- forced-choice Most/Least UI
- option scoring via disc_option_scorings (response_type + disc_code + score_value)
- most/least detail result per dimensi (D/I/S/C/star)
- dominant profile calculation

#### IST Module
- 9 subtest sequential flow (SE, WA, AN, GE, ME, RA, ZR, FA, WU)
- configurable via ist_form_items (randomisasi, multiplier, clue_first, number_of_questions)
- instructions & clues per form/subtest
- per-subtest timer
- random_seed for reproducible randomization
- scoring per category (verbal/numerical/figural) + overall IQ

#### Kraepelin Module
- template form sederhana
- config di attempt level (numbers_per_column, columns_count, duration_per_column)
- random number generation per attempt (kolom + angka)
- timed sequential arithmetic UI
- speed/accuracy/stability/skipped scoring

#### Psychotest Module (pengganti Papikostick)
- hierarki: aspects → characteristics → characteristic_scores
- form → questions → question_options
- weighted mapping: option → aspect + characteristic + weight
- scoring: sum of (weight × option_selected) per characteristic
- 2-level result: per characteristic + per aspect aggregation
- interpretation from characteristic_scores table

### Results & Analytics Module
- history
- score summary
- result interpretation (generic + per-module specific)
- performance trend

### Admin Module
- CRUD test content (generic + DISC + IST + Kraepelin + Psychotest)
- CRUD packages
- user management
- reporting dashboard
- scoring configuration management

---

# 7. Backend Architecture

## 7.1 Laravel Structure
Direkomendasikan struktur berikut:

```text
app/
├── Actions/
├── Enums/
│   ├── EngineType.php
│   ├── DiscCode.php
│   ├── ResponseType.php
│   ├── MatchType.php
│   └── ...
├── Events/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── Billing/
│   │   ├── Catalog/
│   │   ├── TestEngine/
│   │   │   ├── GenericTestController.php
│   │   │   ├── CpnsTestController.php
│   │   │   ├── DiscTestController.php
│   │   │   ├── IstTestController.php
│   │   │   ├── KraepelinTestController.php
│   │   │   └── PsychotestController.php
│   │   ├── Results/
│   │   └── Admin/
│   ├── Middleware/
│   │   ├── EnsureSubscriptionActive.php
│   │   ├── ValidateTestEntitlement.php
│   │   └── ...
│   └── Requests/
├── Jobs/
│   ├── ProcessDiscScoring.php
│   ├── ProcessIstScoring.php
│   ├── ProcessKraepelinScoring.php
│   ├── ProcessPsychotestScoring.php
│   └── ...
├── Models/
│   ├── User.php
│   ├── Test.php
│   ├── TestAttempt.php
│   ├── Disc/
│   │   ├── DiscForm.php
│   │   ├── DiscQuestion.php
│   │   ├── DiscOption.php
│   │   ├── DiscOptionScoring.php
│   │   ├── DiscAttempt.php
│   │   ├── DiscAnswer.php
│   │   └── DiscResult.php
│   ├── Ist/
│   │   ├── IstForm.php
│   │   ├── IstSubtest.php
│   │   ├── IstFormItem.php
│   │   ├── IstInstruction.php
│   │   ├── IstClue.php
│   │   ├── IstSubtestQuestion.php
│   │   ├── IstAttempt.php
│   │   ├── IstSubtestAttempt.php
│   │   ├── IstAnswer.php
│   │   └── IstResult.php
│   ├── Kraepelin/
│   │   ├── KraepelinForm.php
│   │   ├── KraepelinAttempt.php
│   │   ├── KraepelinAttemptColumn.php
│   │   ├── KraepelinAttemptNumber.php
│   │   └── KraepelinAnswer.php
│   └── Psychotest/
│       ├── PsychotestAspect.php
│       ├── PsychotestCharacteristic.php
│       ├── PsychotestCharacteristicScore.php
│       ├── PsychotestForm.php
│       ├── PsychotestQuestion.php
│       ├── PsychotestQuestionOption.php
│       ├── PsychotestOptionCharacteristicMapping.php
│       ├── PsychotestAttempt.php
│       ├── PsychotestAnswer.php
│       ├── PsychotestResultCharacteristic.php
│       └── PsychotestResultAspect.php
├── Policies/
├── Providers/
├── Services/
│   ├── Auth/
│   ├── Billing/
│   ├── Catalog/
│   ├── TestEngine/
│   │   ├── AttemptManager.php
│   │   ├── TimerService.php
│   │   ├── RandomizationService.php
│   │   └── EssayGradingService.php
│   ├── Scoring/
│   │   ├── ScoringServiceInterface.php
│   │   ├── ScoringServiceFactory.php
│   │   ├── GenericScoringService.php
│   │   ├── CpnsScoringService.php
│   │   ├── DiscScoringService.php
│   │   ├── IstScoringService.php
│   │   ├── KraepelinScoringService.php
│   │   └── PsychotestScoringService.php
│   ├── Reporting/
│   └── Admin/
├── Support/
└── ValueObjects/
```

---

## 7.2 Recommended Domain Modules

### Auth Domain
Tanggung jawab:
- login/register
- profile
- authorization

### Billing Domain
Tanggung jawab:
- subscription lifecycle
- invoice
- payment callback/webhook
- entitlement validation (plan_entitlements per program/test_type)

### Catalog Domain
Tanggung jawab:
- programs
- test types (dengan engine_type routing)
- package visibility
- entitlement rules

### Test Engine Domain
Tanggung jawab:
- create attempt
- load questions (generic + specialized per engine_type)
- save answers
- lock/submit attempt
- timer validation (global, per-subtest, per-column)
- essay auto-grading (question_essay_answers: exact/fuzzy/contains/regex)

### Scoring Domain
Tanggung jawab (via Strategy Pattern):
- scoring generic tests (standard objective)
- scoring CPNS (category-based: TWK/TIU/TKP via cpns_score_rules)
- scoring DISC (Most/Least aggregation via disc_option_scorings → most/least per disc_code → final score)
- scoring IST (per-subtest → per-category aggregation → IQ conversion, with multiplier support)
- scoring Kraepelin (speed/accuracy/stability calculation from kraepelin_answers)
- scoring Psychotest (weighted mapping: sum of weight × option_selected per characteristic → aspect aggregation)

### Reporting Domain
Tanggung jawab:
- result summary (generic + per-module specific)
- progress metrics
- analytics aggregation
- interpretation lookup (psychotest_characteristic_scores)

---

## 7.3 Request Flow
Alur request utama:

```text
Browser
  -> Inertia request
    -> Laravel route
      -> Controller
        -> Form Request validation
          -> Service / Action
            -> Model / Query / Domain logic
              -> Response via Inertia page props
```

### Specialized Request Flows

#### DISC Answer Flow
```text
User selects Most/Least options
  -> DiscTestController@saveAnswer
    -> Validate: most ≠ least, both from same question
      -> Save disc_answers (most_option_id, least_option_id)
        -> Auto-save via API
```

#### IST Subtest Flow
```text
User starts IST test
  -> IstTestController@startAttempt
    -> Create ist_attempt (current_subtest_code = 'SE')
      -> Create ist_subtest_attempts for all 9 subtests
        -> Load first subtest questions (with random_seed if randomized)
          -> Start per-subtest timer
            -> On subtest submit/timeout → advance to next subtest
              -> On all 9 complete → run IstScoringService
```

#### Kraepelin Attempt Flow
```text
User starts Kraepelin test
  -> KraepelinTestController@startAttempt
    -> Create kraepelin_attempt (copy config from form)
      -> Generate kraepelin_attempt_columns (N columns)
        -> Generate kraepelin_attempt_numbers (random 0-9 per column)
          -> Start timed column interface
            -> User answers → save kraepelin_answers
              -> On complete → run KraepelinScoringService
```

#### Psychotest Answer Flow
```text
User selects option
  -> PsychotestController@saveAnswer
    -> Save psychotest_answers (option_id)
      -> On submit → run PsychotestScoringService
        -> For each answer: lookup option_characteristic_mappings
          -> Sum weights per characteristic
            -> Aggregate per aspect
              -> Save psychotest_result_characteristics + psychotest_result_aspects
                -> Lookup interpretation from psychotest_characteristic_scores
```

---

# 8. Frontend Architecture

## 8.1 Frontend Pattern
Frontend menggunakan:
- **Inertia.js** untuk server-driven SPA
- **Vue 3 Composition API**
- **shadcn-vue** untuk design system dan reusable UI components

---

## 8.2 Frontend Folder Recommendation

```text
resources/js/
├── Components/
│   ├── ui/                         # shadcn-vue components
│   ├── forms/
│   ├── charts/
│   ├── test/
│   │   ├── GenericQuestion.vue     # MC, essay, true/false
│   │   ├── DiscQuestion.vue        # Most/Least 4 options
│   │   ├── IstSubtestView.vue      # Sequential subtest
│   │   ├── IstInstructionView.vue  # Instructions display
│   │   ├── IstClueView.vue         # Clue with timer
│   │   ├── KraepelinColumn.vue     # Timed arithmetic column
│   │   ├── KraepelinGrid.vue       # Full Kraepelin interface
│   │   ├── PsychotestQuestion.vue  # Option selection
│   │   └── TimerWidget.vue         # Reusable timer
│   └── shared/
├── Layouts/
├── Pages/
│   ├── Auth/
│   ├── Dashboard/
│   ├── Tests/
│   │   ├── Generic/
│   │   ├── Cpns/
│   │   ├── Disc/
│   │   ├── Ist/
│   │   ├── Kraepelin/
│   │   └── Psychotest/
│   ├── Results/
│   │   ├── Generic/
│   │   ├── Cpns/
│   │   ├── Disc/
│   │   │   └── DiscResultDetail.vue  # most/least breakdown
│   │   ├── Ist/
│   │   │   └── IstResultDetail.vue   # per-category + IQ
│   │   ├── Kraepelin/
│   │   │   └── KraepelinResultDetail.vue  # speed/accuracy/stability
│   │   └── Psychotest/
│   │       └── PsychotestResultDetail.vue # per-characteristic + per-aspect
│   ├── Billing/
│   └── Admin/
│       ├── Disc/
│       ├── Ist/
│       ├── Kraepelin/
│       └── Psychotest/
├── Composables/
│   ├── useTimer.ts                 # Reusable timer logic
│   ├── useAutoSave.ts              # Auto-save answer
│   ├── useIstSubtestFlow.ts        # IST sequential flow state
│   ├── useKraepelinEngine.ts       # Kraepelin timed columns state
│   └── useTestNavigation.ts        # Question navigation
├── Lib/
├── Types/
│   ├─��� disc.ts
│   ├── ist.ts
│   ├── kraepelin.ts
│   ├── psychotest.ts
│   └── ...
└── app.ts
```

---

## 8.3 UI Strategy with shadcn-vue
shadcn-vue digunakan untuk:
- button
- input
- card
- table
- dialog
- dropdown
- tabs
- toast
- form components
- progress bar (IST subtest progress, Kraepelin column progress)
- badge (DISC dimension labels, IST category labels)
- chart components wrapper (DISC radar, IST category bars, Kraepelin performance line)

Keuntungan:
- konsisten
- cepat membangun dashboard/admin UI
- mudah di-custom dengan Tailwind

---

## 8.4 State Management
Untuk sebagian besar kebutuhan:
- gunakan **Inertia props**
- gunakan **local component state**
- gunakan **composables**

Gunakan **Pinia** untuk:
- IST subtest flow state (current subtest, progress, timer state per subtest)
- Kraepelin timed column state (current column, timer, answer buffer)
- Test attempt state yang kompleks (multi-step flows)

---

# 9. Database Architecture

## 9.1 Database Choice
Database utama: **PostgreSQL**

Alasan:
- kuat untuk relational data
- bagus untuk query kompleks
- indexing matang
- dukungan JSONB sangat baik (untuk result_payload, answer_json)
- cocok untuk analytics ringan hingga menengah

---

## 9.2 Database Principles
- gunakan UUID (char(36)) untuk entity bisnis utama
- gunakan foreign key untuk integritas data
- gunakan index pada kolom filter penting
- gunakan soft delete pada entity utama tertentu (users, tests, questions, subscription_plans, test_packages)
- gunakan JSONB untuk payload fleksibel bila perlu
- config immutable per attempt (Kraepelin: numbers_per_column/columns_count/duration_per_column disalin ke attempt level)
- random data generation per attempt (Kraepelin: angka random, IST: random_seed untuk reproducible randomization)

---

## 9.3 Main Database Domains

| Domain | Jumlah Tabel | Deskripsi |
|---|---|---|
| Users & Access | 4 | users, user_profiles, roles, model_has_roles |
| Subscription & Billing | 4 | subscription_plans, subscriptions, payments, plan_entitlements |
| Program & Catalog | 5 | programs, test_types, program_test_types, test_packages, test_package_items |
| Generic Test Engine | 8 | tests, test_sections, questions, question_options, question_essay_answers, test_attempts, attempt_answers, test_results |
| CPNS Module | 2 | cpns_test_blueprints, cpns_score_rules |
| DISC Module | 7 | disc_forms, disc_questions, disc_options, disc_option_scorings, disc_attempts, disc_answers, disc_results |
| IST Module | 10 | ist_forms, ist_subtests, ist_form_items, ist_instructions, ist_clues, ist_subtest_questions, ist_attempts, ist_subtest_attempts, ist_answers, ist_results |
| Kraepelin Module | 5 | kraepelin_forms, kraepelin_attempts, kraepelin_attempt_columns, kraepelin_attempt_numbers, kraepelin_answers |
| Psychotest Module | 11 | psychotest_aspects, psychotest_characteristics, psychotest_characteristic_scores, psychotest_forms, psychotest_questions, psychotest_question_options, psychotest_option_characteristic_mappings, psychotest_attempts, psychotest_answers, psychotest_result_characteristics, psychotest_result_aspects |
| Reporting & Support | 3 | user_progress, bookmarks, activity_logs |
| **Total** | **59** | |

---

## 9.4 Key Database Design Decisions

### 9.4.1 DISC: Separated Scoring Table
- `disc_options` hanya menyimpan `option_text` dan `sort_order`
- Scoring logic dipindah ke `disc_option_scorings` (response_type: most/least, disc_code: D/I/S/C/star)
- Alasan: satu option bisa memiliki scoring berbeda untuk Most vs Least response

### 9.4.2 IST: Configuration Pivot Table
- `ist_form_items` sebagai pivot config antara form dan subtest
- Menyimpan: is_randomized, number_of_questions, multiplier, clue_first, duration_minutes, minimum_score
- Alasan: konfigurasi per subtest dalam form bisa berbeda-beda

### 9.4.3 IST: Instructions & Clues
- `ist_instructions` dan `ist_clues` bisa di-attach ke form ATAU subtest (either/or, not both null)
- Alasan: instruksi bisa general untuk seluruh form atau spesifik per subtest

### 9.4.4 Kraepelin: Config at Attempt Level
- `kraepelin_forms` hanya template (title, description)
- Config detail (numbers_per_column, columns_count, duration_per_column) disalin ke `kraepelin_attempts`
- Kolom dan angka di-generate random per attempt (`kraepelin_attempt_columns`, `kraepelin_attempt_numbers`)
- Alasan: setiap attempt immutable, perubahan template tidak mempengaruhi attempt berjalan

### 9.4.5 Psychotest: Two-Level Hierarchy
- `psychotest_aspects` → `psychotest_characteristics` (parent → child)
- Scoring via `psychotest_option_characteristic_mappings` (weighted)
- Result: per characteristic (`psychotest_result_characteristics`) + per aspect (`psychotest_result_aspects`)
- Alasan: granularity scoring yang lebih baik, extensible untuk berbagai tipe psychotest

### 9.4.6 Generic Engine: Essay Auto-Grading
- `question_essay_answers` mendukung auto-grading essay
- Match types: exact, fuzzy (toleransi typo), contains (kata kunci), regex (pattern)
- Multiple acceptable answers dengan priority ordering
- Alasan: mendukung auto-grading untuk soal essay sederhana

---

# 10. Key Components

## 10.1 Authentication Component
Tanggung jawab:
- login/register
- email verification
- session management
- role check

## 10.2 Subscription Access Guard
Tanggung jawab:
- cek apakah user punya akses ke program/test tertentu (via plan_entitlements)
- validasi entitlement sebelum test dimulai
- cek limit attempts per entitlement

## 10.3 Test Attempt Manager
Tanggung jawab:
- create attempt (generic + specialized per engine_type)
- validate active attempt
- set expiration / deadline
- submit final
- **DISC specific:** validate Most ≠ Least selection
- **IST specific:** manage sequential subtest flow, advance current_subtest_code
- **Kraepelin specific:** generate random columns and numbers, manage timed column transitions
- **Psychotest specific:** track answer completion

## 10.4 Scoring Engine
Tanggung jawab:
- hitung nilai berdasarkan tipe test
- simpan result summary
- hasil interpretasi

Gunakan **Strategy Pattern** dengan factory:

```text
ScoringServiceFactory
  -> resolves by engine_type:
     - 'generic'    → GenericScoringService
     - 'cpns'       → CpnsScoringService (via generic + cpns_score_rules)
     - 'disc'       → DiscScoringService
     - 'ist'        → IstScoringService
     - 'kraepelin'  → KraepelinScoringService
     - 'psychotest' → PsychotestScoringService
```

### 10.4.1 GenericScoringService
- standard objective scoring: is_correct count × score
- essay auto-grading via question_essay_answers (match_type: exact/fuzzy/contains/regex)

### 10.4.2 CpnsScoringService
- extends generic scoring
- applies cpns_score_rules per category_code (TWK/TIU/TKP)
- correct_score, wrong_score, empty_score per category
- checks passing_score from cpns_test_blueprints

### 10.4.3 DiscScoringService
Input: disc_answers (most_option_id, least_option_id per question)
Process:
1. For each answer, lookup disc_option_scorings for most_option_id (response_type='most')
2. For each answer, lookup disc_option_scorings for least_option_id (response_type='least')
3. Aggregate by disc_code: sum most_d, most_i, most_s, most_c, most_star
4. Aggregate by disc_code: sum least_d, least_i, least_s, least_c, least_star
5. Calculate final: score_d = most_d - least_d (dan seterusnya)
6. Determine dominant_profile
Output: disc_results row

### 10.4.4 IstScoringService
Input: ist_answers per subtest
Process:
1. Score each subtest: correct answers × multiplier (from ist_form_items)
2. Check minimum_score per subtest (if configured)
3. Calculate category scores:
   - Verbal = (SE + WA + AN + GE) / 4
   - Numerical = (ME + RA + ZR) / 3
   - Figural = (FA + WU) / 2
4. Calculate overall + IQ conversion (age_norm function)
5. Calculate percentile per category
Output: ist_results rows (verbal, numerical, figural, overall) + ist_attempts.iq_score

### 10.4.5 KraepelinScoringService
Input: kraepelin_answers (top_number, bottom_number, user_answer, correct_answer)
Process:
1. Count total_answered, total_correct, total_wrong, total_skipped
2. Calculate speed_score (answers per unit time)
3. Calculate accuracy_score (correct / answered ratio)
4. Calculate stability_score (variance of correct answers across columns)
5. Calculate final_score (weighted combination)
Output: kraepelin_attempts fields updated

### 10.4.6 PsychotestScoringService
Input: psychotest_answers (option selections)
Process:
1. For each answer, lookup psychotest_option_characteristic_mappings
2. For each mapping: accumulate weight per characteristic
3. Calculate raw_score per characteristic: sum of weights
4. Scale raw_score to scaled_score per characteristic
5. Lookup interpretation from psychotest_characteristic_scores
6. Aggregate characteristics per aspect: calculate aspect raw_score and scaled_score
Output: psychotest_result_characteristics rows + psychotest_result_aspects rows

## 10.5 Admin Content Manager
Tanggung jawab:
- CRUD program
- CRUD test
- CRUD question bank (generic + essay answer keys)
- publish/unpublish tests
- **DISC:** CRUD forms, questions, options, option_scorings
- **IST:** CRUD forms, subtests, form_items config, instructions, clues, subtest_questions
- **Kraepelin:** CRUD forms (template)
- **Psychotest:** CRUD aspects, characteristics, characteristic_scores, forms, questions, question_options, option_characteristic_mappings

## 10.6 Randomization Service
Tanggung jawab:
- generate random question order using seed (IST: ist_subtest_attempts.random_seed)
- generate random numbers for Kraepelin columns (kraepelin_attempt_numbers.value 0-9)
- ensure reproducibility: same seed = same order (for review/debugging)

## 10.7 Essay Grading Service
Tanggung jawab:
- auto-grade essay answers using question_essay_answers
- support match types: exact, fuzzy (Levenshtein distance), contains (substring), regex
- priority-based matching: check higher priority answers first
- return score from first matching answer

---

# 11. Data Flow Scenarios

## 11.1 User Registration
```text
User -> Register Page -> Laravel Auth Controller -> User created -> Email verification
```

## 11.2 Subscription Purchase
```text
User -> Plan Page -> Checkout -> Payment Gateway -> Callback/Webhook -> Subscription activated -> plan_entitlements applied
```

## 11.3 Start Generic/CPNS Test
```text
User -> Test Detail -> Access validation (entitlement check) -> Create test_attempt -> Load questions -> Start timer
```

## 11.4 Submit Generic/CPNS Test
```text
User -> Submit answers -> Lock attempt -> Run GenericScoringService/CpnsScoringService -> Store test_results -> Show result page
```

## 11.5 DISC Test Flow
```text
User -> Select DISC test -> Access validation
  -> Create test_attempt + disc_attempt
    -> Load disc_questions + disc_options (4 per question)
      -> User selects Most + Least per question
        -> Save disc_answers (most_option_id, least_option_id)
          -> Submit → Run DiscScoringService
            -> Lookup disc_option_scorings per answer
              -> Aggregate most/least per disc_code
                -> Calculate final scores (most - least)
                  -> Store disc_results (most/least detail + dominant_profile)
                    -> Show DISC result page (radar chart + interpretation)
```

## 11.6 IST Test Flow
```text
User -> Select IST test -> Access validation
  -> Create test_attempt + ist_attempt (current_subtest_code = 'SE')
    -> Create 9 ist_subtest_attempts
      -> Display ist_instructions (form-level)
        -> For each subtest (1-9):
          -> Display ist_instructions (subtest-level)
          -> Display ist_clues (if clue_first = true)
          -> Load questions (randomized by random_seed if is_randomized)
          -> Start per-subtest timer (from ist_form_items.duration_minutes)
          -> User answers questions → save ist_answers
          -> On submit/timeout:
            -> Score subtest (raw_score × multiplier)
            -> Advance to next subtest (update current_subtest_code)
        -> After all 9 subtests:
          -> Run IstScoringService
            -> Calculate category scores (verbal/numerical/figural)
            -> Calculate overall + IQ conversion
            -> Store ist_results
            -> Update ist_attempts.total_score + iq_score
              -> Show IST result page (category bars + IQ + interpretation)
```

## 11.7 Kraepelin Test Flow
```text
User -> Select Kraepelin test -> Access validation
  -> Create test_attempt + kraepelin_attempt
    -> Copy config from form to attempt (numbers_per_column, columns_count, duration_per_column)
      -> Generate kraepelin_attempt_columns (N columns)
        -> Generate kraepelin_attempt_numbers (random 0-9 per column position)
          -> Start timed column interface
            -> Per column:
              -> Display pairs of numbers (top + bottom)
              -> User enters sum (ones digit)
              -> Save kraepelin_answers (top_number, bottom_number, user_answer, correct_answer, is_correct)
              -> On column timer end → auto-advance to next column
            -> After all columns complete:
              -> Run KraepelinScoringService
                -> Calculate speed, accuracy, stability, final scores
                -> Update kraepelin_attempt totals
                  -> Show Kraepelin result page (performance line chart + metrics)
```

## 11.8 Psychotest Test Flow
```text
User -> Select Psychotest test -> Access validation
  -> Create test_attempt + psychotest_attempt
    -> Load psychotest_questions (active only, ordered by number)
      -> Load psychotest_question_options per question (label, statement, sort_order)
        -> User selects option per question
          -> Save psychotest_answers (option_id, answered_at)
            -> On submit → Run PsychotestScoringService
              -> For each answer:
                -> Lookup psychotest_option_characteristic_mappings (option → aspect + characteristic + weight)
                -> Accumulate weight per characteristic
              -> Calculate per characteristic:
                -> raw_score = sum of accumulated weights
                -> scaled_score = scale function
                -> Lookup interpretation from psychotest_characteristic_scores
              -> Aggregate per aspect:
                -> raw_score = aggregation of characteristic raw_scores
                -> scaled_score = aggregation of characteristic scaled_scores
              -> Store psychotest_result_characteristics
              -> Store psychotest_result_aspects
                -> Show Psychotest result page (per-characteristic + per-aspect breakdown + interpretations)
```

---

# 12. API / Route Strategy

## 12.1 Web Routes First
Karena menggunakan Inertia, mayoritas route akan memakai:
- `web.php`
- controller returning `Inertia::render()`

## 12.2 API Usage
Route API digunakan untuk:
- autosave jawaban (generic, DISC most/least, IST per-subtest, Kraepelin per-column, Psychotest per-question)
- payment webhook
- async data fetch tertentu
- IST subtest transition (advance to next subtest)
- Kraepelin column transition (auto-advance on timer)
- charts/metrics jika dibutuhkan
- future mobile integration

## 12.3 Route Organization

```text
# Web Routes (Inertia)
/tests/{slug}                          # Test detail + start
/tests/{slug}/attempt/{id}             # Generic test taking
/tests/{slug}/disc/{id}                # DISC test taking
/tests/{slug}/ist/{id}                 # IST test taking
/tests/{slug}/kraepelin/{id}           # Kraepelin test taking
/tests/{slug}/psychotest/{id}          # Psychotest test taking
/results/{attemptId}                   # Result page (routes to correct type)

# API Routes (JSON)
POST /api/attempts/{id}/answers        # Generic auto-save
POST /api/disc/{id}/answers            # DISC auto-save (most/least)
POST /api/ist/{id}/subtests/{code}/answers  # IST auto-save per subtest
POST /api/ist/{id}/subtests/{code}/submit   # IST submit subtest
POST /api/kraepelin/{id}/answers       # Kraepelin auto-save per column
POST /api/psychotest/{id}/answers      # Psychotest auto-save

# Admin Routes (Inertia via Filament or custom)
/admin/disc/forms/...                  # DISC admin
/admin/ist/forms/...                   # IST admin
/admin/kraepelin/forms/...             # Kraepelin admin
/admin/psychotest/aspects/...          # Psychotest admin
/admin/psychotest/forms/...            # Psychotest admin
```

---

# 13. Security Architecture

## 13.1 Authentication Security
- password hashing dengan bcrypt/argon
- CSRF protection
- session protection
- email verification

## 13.2 Authorization
- gunakan Policies / Gates
- role-based access untuk admin
- ownership validation untuk attempt dan result
- entitlement validation via plan_entitlements (per program + test_type)

## 13.3 Payment Security
- verifikasi signature webhook
- simpan raw callback payload (payments.payload JSON)
- pastikan idempotency pada webhook handling

## 13.4 Test Integrity
- validasi attempt status sebelum save answer
- batasi double submit
- simpan timestamp jawaban (answered_at di semua answer tables)
- logging aktivitas penting (activity_logs)
- randomisasi soal dengan reproducible seed (ist_subtest_attempts.random_seed)
- DISC validation: most_option_id ≠ least_option_id
- IST validation: subtest harus dikerjakan berurutan
- Kraepelin: config immutable per attempt (perubahan template tidak affect attempt berjalan)
- deadline enforcement: auto-expire attempts past deadline_at

---

# 14. Performance & Scalability

## 14.1 Caching
Gunakan Redis untuk:
- cache katalog yang jarang berubah (programs, test_types)
- cache entitlement user (plan_entitlements)
- cache result summary tertentu
- cache psychotest aspects + characteristics hierarchy (jarang berubah)
- cache IST form config (ist_form_items — jarang berubah)
- cache DISC option scorings per form (jarang berubah)
- queue backend

## 14.2 Queue Usage
Gunakan queue untuk:
- kirim email
- proses payment callback berat
- generate report
- update analytics agregat (user_progress)
- **proses scoring berat:**
  - IST scoring (9 subtest aggregation + IQ conversion)
  - Psychotest scoring (weighted mapping calculation + 2-level aggregation)
  - Kraepelin scoring (speed/accuracy/stability calculation)
- **Kraepelin number generation** (jika jumlah kolom × angka besar)

## 14.3 Database Optimization
- index di kolom relasi dan filter utama
- composite indexes untuk query umum:
  - `(disc_option_id, response_type)` di disc_option_scorings
  - `(ist_form_id, sort_order)` di ist_form_items
  - `(psychotest_option_id, psychotest_characteristic_id)` di mappings
  - `(kraepelin_attempt_id, column_number)` di attempt_columns
- paginasi untuk admin tables
- eager loading untuk relasi penting
- materialized/report tables bila analytics berkembang

---

# 15. Error Handling & Logging

## 15.1 Error Handling
- gunakan exception khusus per domain:
  - `AttemptExpiredException`
  - `EntitlementDeniedException`
  - `ScoringCalculationException`
  - `InvalidDiscSelectionException` (most = least)
  - `SubtestOutOfOrderException` (IST)
  - `KraepelinTimerExpiredException`
- tampilkan pesan user-friendly
- pisahkan business exception dan system exception

## 15.2 Logging
Log yang perlu dicatat:
- auth events
- payment events
- attempt lifecycle (start, save, submit, expire)
- scoring failures (per engine_type)
- admin actions (CRUD operations)
- suspicious behavior
- IST subtest transitions
- Kraepelin column transitions
- timer events (start, expire, extend)

---

# 16. Deployment Architecture

## 16.1 Development
- Docker Compose
- Laravel app container
- Node/Vite container
- PostgreSQL
- Redis
- Mailpit

## 16.2 Production
Minimal setup:
- Nginx
- PHP-FPM
- PostgreSQL
- Redis
- queue worker (dedicated, untuk scoring jobs)
- scheduler
- object storage optional (untuk media_url di questions)

---

## 16.3 Deployment Pipeline
Langkah ideal:
1. test (unit + feature + scoring service tests)
2. static analysis (PHPStan / Larastan)
3. build assets (Vite)
4. run migrations
5. deploy application
6. restart workers (queue workers untuk scoring jobs)
7. health check

---

# 17. Testing Strategy

## 17.1 Backend Testing

### Unit Tests (Critical)
- **GenericScoringService** — standard objective scoring
- **CpnsScoringService** — category-based scoring with cpns_score_rules
- **DiscScoringService** — Most/Least aggregation, disc_code calculation, dominant profile
- **IstScoringService** — per-subtest scoring with multiplier, category aggregation, IQ conversion
- **KraepelinScoringService** — speed/accuracy/stability calculation
- **PsychotestScoringService** — weighted mapping calculation, characteristic + aspect aggregation
- **EssayGradingService** — exact/fuzzy/contains/regex matching
- **RandomizationService** — seed-based reproducibility

### Feature Tests
- auth flow (register, login, email verify)
- billing flow (plan selection, payment, subscription activation, entitlement)
- DISC attempt flow (create attempt, save Most/Least answers, submit, verify results)
- IST attempt flow (create attempt, sequential subtest advancement, per-subtest save/submit)
- Kraepelin attempt flow (create attempt, number generation verification, answer save, scoring)
- Psychotest attempt flow (create attempt, option selection, weighted scoring verification)

### Integration Tests
- payment gateway callbacks (webhook verification, idempotency)
- scoring pipeline end-to-end (create attempt → save answers → submit → verify result)

## 17.2 Frontend Testing
- component tests untuk UI penting:
  - DiscQuestion.vue (Most/Least selection, validation Most ≠ Least)
  - IstSubtestView.vue (sequential flow, timer)
  - KraepelinColumn.vue (timed input, auto-advance)
  - PsychotestQuestion.vue (option selection)
  - TimerWidget.vue (countdown accuracy)
- form behavior tests
- smoke test halaman utama

## 17.3 End-to-End
Direkomendasikan untuk flow:
- login → purchase → start CPNS test → answer → submit → result display
- login → start DISC test → answer Most/Least → submit → verify DISC profile result
- login → start IST test → complete 9 subtests sequentially → verify category scores + IQ
- login → start Kraepelin test → answer timed columns → verify speed/accuracy/stability
- login → start Psychotest → answer all questions → verify characteristic + aspect scores

---

# 18. Risks & Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| scoring logic salah | hasil tes tidak valid | pisahkan scoring service per engine_type, test unit secara ketat per scoring service |
| query berat di analytics | performa menurun | agregasi periodik, cache, index, materialized views |
| webhook payment gagal | subscription tidak aktif | retry + idempotency + logging |
| state test tidak sinkron | jawaban hilang | autosave + atomic submit per engine_type |
| scope terlalu luas | development lambat | phased delivery: MVP-A (CPNS+DISC+IST), MVP-B (Kraepelin+Psychotest) |
| DISC Most/Least validation gagal | invalid DISC result | frontend + backend validation: most ≠ least |
| IST subtest timer drift | unfair testing time | server-side timer validation, deadline_at enforcement |
| Kraepelin random numbers tidak random | predictable test | cryptographic random for kraepelin_attempt_numbers.value |
| Psychotest weighted scoring error | incorrect personality profile | comprehensive unit tests, weight validation on admin input |
| IST random_seed collision | same questions for different attempts | use unique seed per attempt (e.g., attempt_id hash) |
| Kraepelin config change during test | inconsistent test conditions | config immutable: copied to attempt level at start |

---

# 19. Architectural Decisions Summary

| Decision | Choice | Reason |
|---|---|---|
| Backend framework | Laravel | cepat, matang, ecosystem kuat |
| Frontend bridge | Inertia.js | efisien untuk server-driven SPA |
| Frontend framework | Vue 3 | ringan, fleksibel |
| UI system | shadcn-vue | modern, reusable, konsisten |
| Database | PostgreSQL | relational kuat + JSONB |
| Architecture style | Modular Monolith | cocok untuk MVP dan scale bertahap |
| Async processing | Redis Queue | stabil untuk task background |
| Scoring pattern | Strategy Pattern + Factory | extensible per engine_type tanpa if/else chain |
| DISC scoring | Separated scoring table | satu option bisa punya scoring berbeda per response_type |
| IST config | Pivot table (ist_form_items) | konfigurasi per subtest bisa berbeda per form |
| Kraepelin data | Config at attempt level | immutability, setiap attempt independen |
| Psychotest hierarchy | aspects → characteristics | granularity scoring lebih baik, extensible |
| Papikostick replacement | Psychotest Module | lebih generik, mendukung berbagai tipe personality test |
| Essay grading | Auto-grade with match_type | mendukung simple auto-grading tanpa AI |
| Randomization | Seed-based | reproducible untuk review/debugging |

---

# 20. Recommended Next Documents

Setelah SAD ini, dokumen lanjutan yang sebaiknya dibuat:

1. ✅ `database-schema-saas-cpns-bumn_Version3.md` — sudah dibuat
2. ✅ `changelog-database-schema-v2-to-v3.md` — sudah dibuat
3. ✅ `feature-backlog-mvp_Version3.md` — sudah dibuat
4. ✅ `implementation-plan_Version3.md` — sudah dibuat
5. `erd-saas-cpns-bumn.md` — high-level ERD berdasarkan database schema V3
6. `laravel-migration-plan.md` — migration order dan script plan
7. `scoring-service-design.md` — detail strategy pattern per engine_type
8. `frontend-architecture.md` — detail Vue components, composables, Pinia stores
9. `service-layer-design.md` — detail service classes dan interfaces
10. `deployment-architecture.md` — detail Docker, CI/CD, monitoring

---

# 21. Conclusion
Arsitektur yang dipilih — **Laravel + Inertia.js + Vue 3 + shadcn-vue + PostgreSQL** — sangat cocok untuk membangun SaaS latihan tes CPNS & BUMN karena:

- cepat untuk dikembangkan
- maintainable untuk tim kecil/menengah
- cukup fleksibel untuk banyak jenis tes
- siap dikembangkan bertahap dari MVP ke skala lebih besar

Pendekatan **modular monolith** dengan **strategy pattern untuk scoring engine** memberi keseimbangan terbaik antara:
- kecepatan delivery
- struktur domain yang rapi
- kesiapan scaling di masa depan
- extensibility untuk tipe test baru

Setiap specialized module (DISC, IST, Kraepelin, Psychotest) memiliki:
- tabel database tersendiri (config + execution + result)
- scoring service tersendiri (strategy pattern)
- frontend components tersendiri
- admin management tersendiri

Ini memastikan bahwa:
- penambahan modul baru tidak memerlukan refactor besar
- scoring logic terisolasi dan mudah ditest
- setiap modul bisa dikembangkan dan di-deploy secara independen
- kode tetap maintainable seiring pertumbuhan fitur

---

# 22. Perubahan dari Version 1.0.0

| Section | Perubahan |
|---|---|
| 1.3 Business Context | Papikostick → Psychotest |
| 2.1 Primary Goals | Ditambah: config immutable per attempt, strategy pattern scoring |
| 2.2 Non-Functional Goals | Ditambah: per-subtest timer, timed column, weighted scoring precision |
| 3.1 Actors | Admin diperkaya: scoring config management |
| 3.2 Context Diagram | Ditambah module list |
| 4.3 Architectural Pattern | Ditambah: Strategy pattern untuk scoring |
| 5.2 Frontend | Ditambah: Pinia recommendation untuk IST/Kraepelin |
| 6.1 Logical Layers | Diperkaya: test UI types, scoring module list |
| 6.2 Module Overview | Ditambah: Specialized Test Modules (DISC, IST, Kraepelin, Psychotest) detail |
| 7.1 Laravel Structure | Diperbarui: Model grouping per module, Scoring services detail, Jobs per module, Enums, Middleware |
| 7.2 Domain Modules | Scoring Domain: Papikostick → Psychotest, diperkaya detail per service |
| 7.3 Request Flow | Ditambah: Specialized request flows (DISC, IST, Kraepelin, Psychotest) |
| 8.2 Frontend Folder | Diperbarui: test components per module, result pages per module, composables, types |
| 8.4 State Management | Pinia direkomendasikan untuk IST subtest flow dan Kraepelin timed columns |
| 9.3 Database Domains | Diperbarui: tabel count per domain, total 59 tabel |
| 9.4 Key DB Decisions | **Baru**: 6 key design decisions documented |
| 10.4 Scoring Engine | Diperbarui: 6 scoring services dengan detail algorithm per service |
| 10.6 Randomization Service | **Baru** |
| 10.7 Essay Grading Service | **Baru** |
| 11.5-11.8 Data Flows | **Baru**: DISC, IST, Kraepelin, Psychotest detail flows |
| 12.3 Route Organization | **Baru**: detail route structure per module |
| 13.4 Test Integrity | Diperkaya: DISC validation, IST sequential, Kraepelin immutable, deadline enforcement |
| 14.1 Caching | Diperkaya: psychotest hierarchy, IST config, DISC scorings |
| 14.2 Queue | Diperkaya: scoring jobs per module |
| 14.3 DB Optimization | Diperkaya: composite indexes per module |
| 15.1 Error Handling | Diperkaya: domain-specific exceptions |
| 15.2 Logging | Diperkaya: IST subtest transitions, Kraepelin column transitions, timer events |
| 17.1 Backend Testing | Diperkaya: unit tests per scoring service, feature tests per module |
| 17.3 E2E | Diperkaya: E2E per module flow |
| 18 Risks | Diperkaya: DISC/IST/Kraepelin/Psychotest specific risks |
| 19 Decisions | Diperkaya: 8 new decisions documented |
| 22 Changelog | **Baru** |