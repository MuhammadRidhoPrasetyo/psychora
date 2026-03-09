# Software Architecture Document (SAD)
## SaaS Platform Latihan Tes CPNS & BUMN

**Version:** 1.0.0  
**Date:** 2026-03-07  
**Stack:** Laravel + Inertia.js + Vue 3 + shadcn-vue + PostgreSQL

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
- modul latihan CPNS
- modul psikotes BUMN
- admin panel untuk manajemen konten, tes, soal, dan user
- engine pengerjaan test
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
- Papikostick

---

# 2. Architectural Goals

## 2.1 Primary Goals
Arsitektur harus memenuhi tujuan berikut:

1. **Modular**
   - test engine harus bisa mendukung banyak jenis tes
   - modul baru bisa ditambahkan tanpa refactor besar

2. **Maintainable**
   - kode mudah dipahami dan dikelola
   - domain terpisah dengan jelas

3. **Scalable**
   - mampu menangani pertumbuhan user, soal, dan attempt
   - siap untuk scaling vertikal/horizontal bertahap

4. **Secure**
   - proteksi akses user
   - validasi subscription
   - keamanan auth, payment, dan result data

5. **Fast Development**
   - mendukung iterasi cepat untuk MVP dan fase lanjutan

---

## 2.2 Non-Functional Goals
- response time cepat untuk dashboard dan test interface
- auto-save jawaban stabil
- integritas hasil test terjaga
- deployment mudah
- observability dasar tersedia

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
- mengerjakan tes
- melihat hasil

### Admin / Super Admin
- mengelola user
- mengelola paket
- mengelola test
- mengelola bank soal
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
- Pinia (opsional, jika state makin kompleks)

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
+------------------------------------------------------+
| Application Layer                                    |
| Controllers, Form Requests, Actions, Services        |
+------------------------------------------------------+
| Domain Layer                                         |
| Modules: Auth, Billing, Test Engine, Results, Admin  |
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
- access entitlement

### Catalog Module
- programs
- test types
- packages
- list available tests

### Test Engine Module
- start attempt
- question rendering
- answer submission
- auto-save
- timer
- scoring

### Results & Analytics Module
- history
- score summary
- result interpretation
- performance trend

### Admin Module
- CRUD test content
- CRUD packages
- user management
- reporting dashboard

---

# 7. Backend Architecture

## 7.1 Laravel Structure
Direkomendasikan struktur berikut:

```text
app/
├── Actions/
├── Enums/
├── Events/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   ├── Requests/
├── Jobs/
├── Models/
├── Policies/
├── Providers/
├── Services/
│   ├── Auth/
│   ├── Billing/
│   ├── Catalog/
│   ├── TestEngine/
│   ├── Scoring/
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

### Catalog Domain
Tanggung jawab:
- programs
- test types
- package visibility
- entitlement rules

### Test Engine Domain
Tanggung jawab:
- create attempt
- load questions
- save answers
- lock/submit attempt
- timer validation

### Scoring Domain
Tanggung jawab:
- scoring generic tests
- scoring CPNS
- scoring DISC
- scoring IST
- scoring Kraepelin
- scoring Papikostick

### Reporting Domain
Tanggung jawab:
- result summary
- progress metrics
- analytics aggregation

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
│   ├── ui/
│   ├── forms/
│   ├── charts/
│   └── shared/
├── Layouts/
├── Pages/
│   ├── Auth/
│   ├── Dashboard/
│   ├── Tests/
│   ├── Results/
│   ├── Billing/
│   └── Admin/
├── Composables/
├── Lib/
├── Types/
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

Jika state lintas halaman bertambah kompleks:
- tambahkan **Pinia**

---

# 9. Database Architecture

## 9.1 Database Choice
Database utama: **PostgreSQL**

Alasan:
- kuat untuk relational data
- bagus untuk query kompleks
- indexing matang
- dukungan JSONB sangat baik
- cocok untuk analytics ringan hingga menengah

---

## 9.2 Database Principles
- gunakan UUID untuk entity bisnis utama
- gunakan foreign key untuk integritas data
- gunakan index pada kolom filter penting
- gunakan soft delete pada entity utama tertentu
- gunakan JSONB untuk payload fleksibel bila perlu

---

## 9.3 Main Database Domains
- users & access
- billing
- catalog
- generic test engine
- specialized psychotest modules
- analytics & logs

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
- cek apakah user punya akses ke program/test tertentu
- validasi entitlement sebelum test dimulai

## 10.3 Test Attempt Manager
Tanggung jawab:
- create attempt
- validate active attempt
- set expiration
- submit final

## 10.4 Scoring Engine
Tanggung jawab:
- hitung nilai berdasarkan tipe test
- simpan result summary
- hasil interpretasi

Gunakan strategy pattern:
- GenericScoringService
- CpnsScoringService
- DiscScoringService
- IstScoringService
- KraepelinScoringService
- PapikostickScoringService

## 10.5 Admin Content Manager
Tanggung jawab:
- CRUD program
- CRUD test
- CRUD question bank
- publish/unpublish tests

---

# 11. Data Flow Scenarios

## 11.1 User Registration
```text
User -> Register Page -> Laravel Auth Controller -> User created -> Email verification
```

## 11.2 Subscription Purchase
```text
User -> Plan Page -> Checkout -> Payment Gateway -> Callback/Webhook -> Subscription activated
```

## 11.3 Start Test
```text
User -> Test Detail -> Access validation -> Create attempt -> Load questions -> Start timer
```

## 11.4 Submit Test
```text
User -> Submit answers -> Lock attempt -> Run scoring service -> Store test_results -> Show result page
```

---

# 12. API / Route Strategy

## 12.1 Web Routes First
Karena menggunakan Inertia, mayoritas route akan memakai:
- `web.php`
- controller returning `Inertia::render()`

## 12.2 API Usage
Route API digunakan untuk:
- autosave jawaban
- payment webhook
- async data fetch tertentu
- charts/metrics jika dibutuhkan
- future mobile integration

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

## 13.3 Payment Security
- verifikasi signature webhook
- simpan raw callback payload
- pastikan idempotency pada webhook handling

## 13.4 Test Integrity
- validasi attempt status
- batasi double submit
- simpan timestamp jawaban
- logging aktivitas penting
- optional randomisasi soal

---

# 14. Performance & Scalability

## 14.1 Caching
Gunakan Redis untuk:
- cache katalog yang jarang berubah
- cache entitlement user
- cache result summary tertentu
- queue backend

## 14.2 Queue Usage
Gunakan queue untuk:
- kirim email
- proses payment callback berat
- generate report
- update analytics agregat
- proses scoring tertentu jika berat

## 14.3 Database Optimization
- index di kolom relasi dan filter utama
- paginasi untuk admin tables
- eager loading untuk relasi penting
- materialized/report tables bila analytics berkembang

---

# 15. Error Handling & Logging

## 15.1 Error Handling
- gunakan exception khusus per domain
- tampilkan pesan user-friendly
- pisahkan business exception dan system exception

## 15.2 Logging
Log yang perlu dicatat:
- auth events
- payment events
- attempt lifecycle
- scoring failures
- admin actions
- suspicious behavior

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
- queue worker
- scheduler
- object storage optional

---

## 16.3 Deployment Pipeline
Langkah ideal:
1. test
2. static analysis
3. build assets
4. run migrations
5. deploy application
6. restart workers
7. health check

---

# 17. Testing Strategy

## 17.1 Backend Testing
- unit tests untuk service scoring
- feature tests untuk auth, billing, attempts
- integration tests untuk payment callbacks

## 17.2 Frontend Testing
- component tests untuk UI penting
- form behavior tests
- smoke test halaman utama

## 17.3 End-to-End
Direkomendasikan untuk flow:
- login
- purchase
- start test
- answer test
- submit test
- result display

---

# 18. Risks & Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| scoring logic salah | hasil tes tidak valid | pisahkan scoring service dan test unit secara ketat |
| query berat di analytics | performa menurun | agregasi periodik, cache, index |
| webhook payment gagal | subscription tidak aktif | retry + idempotency + logging |
| state test tidak sinkron | jawaban hilang | autosave + atomic submit |
| scope terlalu luas | development lambat | phased delivery / MVP dulu |

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

---

# 20. Recommended Next Documents

Setelah SAD ini, dokumen lanjutan yang sebaiknya dibuat:

1. `database-schema-saas-cpns-bumn.md`
2. `app-model-relationship-map.md`
3. `laravel-migration-plan.md`
4. `frontend-architecture.md`
5. `service-layer-design.md`
6. `deployment-architecture.md`

---

# 21. Conclusion
Arsitektur yang dipilih — **Laravel + Inertia.js + Vue 3 + shadcn-vue + PostgreSQL** — sangat cocok untuk membangun SaaS latihan tes CPNS & BUMN karena:

- cepat untuk dikembangkan
- maintainable untuk tim kecil/menengah
- cukup fleksibel untuk banyak jenis tes
- siap dikembangkan bertahap dari MVP ke skala lebih besar

Pendekatan **modular monolith** memberi keseimbangan terbaik antara:
- kecepatan delivery
- struktur domain yang rapi
- kesiapan scaling di masa depan