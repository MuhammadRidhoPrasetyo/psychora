# Implementation Plan SaaS Latihan Tes CPNS & BUMN

> Dokumen ini menjelaskan rencana implementasi platform SaaS latihan tes untuk peserta seleksi CPNS/PNS dan BUMN.
> Platform mendukung berbagai jenis test seperti TWK/TIU/TKP, DISC, IST, Kraepelin, dan Papikostick.
> Stack utama: Laravel, Inertia.js, Vue.js.

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
- Papikostick
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
- Papikostick

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

Fitur spesifik:
- **CPNS:** objective multiple choice
- **DISC:** pilihan berbasis profil/scoring mapping
- **IST:** subtest-based structure
- **Kraepelin:** timed sequential arithmetic interface
- **Papikostick:** personality inventory dengan scoring profile

## 8.7 Result & Analytics
Fitur:
- skor per attempt
- grafik perkembangan
- hasil per kategori tes
- interpretasi psikotes
- history latihan
- strength/weakness insight

## 8.8 Admin Panel
Fitur:
- dashboard statistik
- CRUD program
- CRUD test types
- CRUD test packages
- CRUD bank soal
- mapping soal ke paket/simulasi
- pengelolaan transaksi
- pengelolaan user
- pengelolaan banner/landing content

---

## 9. Test Engine Design Strategy

Karena Anda ingin menggabungkan CPNS dan BUMN, maka test engine perlu dibagi menjadi 2 layer:

## 9.1 Generic Layer
Dipakai semua tes:
- test
- test_sections
- questions
- question_options
- test_attempts
- attempt_answers
- results

## 9.2 Specialized Layer
Dipakai tes tertentu yang punya kebutuhan khusus:

### CPNS Module
- multiple choice standard
- skor benar/salah / bobot
- pembahasan per soal

### DISC Module
- option-to-profile mapping
- dimensi D/I/S/C
- hasil profil dominan

### IST Module
- subtest structure
- kategori verbal/numerical/figural
- scoring dan konversi

### Kraepelin Module
- kolom angka
- timing per kolom/halaman
- akurasi, speed, consistency

### Papikostick Module
- personality inventory statements
- dimension scoring
- profile interpretation

---

## 10. Papikostick Integration Strategy

Karena Papikostick termasuk tes yang cukup spesifik, saya sarankan jangan dipaksa ke struktur multiple-choice generik sepenuhnya.

Gunakan pendekatan:
- tabel master form papikostick
- tabel statements/items
- tabel dimension/profile mapping
- tabel user answers
- tabel result scoring
- tabel interpretation output

Dengan begitu:
- lebih mudah maintain
- lebih akurat untuk scoring psikotes
- bisa dikembangkan untuk report psikologis sederhana

---

## 11. Usulan Struktur Data Tingkat Tinggi

Berikut domain entity yang sebaiknya ada setelah update scope:

### User & Access
- users
- roles
- permissions
- user_profiles

### Billing
- subscription_plans
- subscriptions
- payments
- invoices

### Program & Product
- programs
- test_types
- test_packages
- package_items

### Generic Test Engine
- tests
- test_sections
- questions
- question_options
- question_explanations
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
- disc_option_scores
- disc_results

### IST Specific
- ist_forms
- ist_subtests
- ist_questions
- ist_results

### Kraepelin Specific
- kraepelin_forms
- kraepelin_columns
- kraepelin_numbers
- kraepelin_results

### Papikostick Specific
- papikostick_forms
- papikostick_items
- papikostick_dimensions
- papikostick_item_mappings
- papikostick_answers
- papikostick_results

### Supportive
- bookmarks
- user_progress
- announcements
- activity_logs

---

## 12. MVP Recommendation

Agar tidak terlalu berat, saya sarankan MVP dibagi dalam 2 fase bisnis:

## Phase MVP-A
Fokus:
- landing page
- auth
- user dashboard
- subscription
- CPNS basic module
- BUMN module untuk DISC + IST
- admin CRUD soal
- result history

## Phase MVP-B
Tambahan:
- Kraepelin
- Papikostick
- full simulation package
- analytics improvement
- advanced report interpretation

Alasan:
- Kraepelin dan Papikostick biasanya perlu UI/scoring yang lebih khusus
- lebih aman jika dimasukkan setelah fondasi generic test engine stabil

---

## 13. Fase Implementasi Detail

## Phase 1 - Project Foundation
- setup Laravel + Inertia + Vue
- setup auth
- setup role & permission
- setup base layout
- setup admin panel foundation

## Phase 2 - Core Business Modules
- programs
- test types
- packages
- subscriptions
- payments
- access control

## Phase 3 - Generic Test Engine
- tests
- sections
- questions
- options
- attempts
- answers
- scoring engine sederhana

## Phase 4 - CPNS Module
- TWK/TIU/TKP structure
- pembahasan soal
- tryout simulation
- ranking/summary sederhana

## Phase 5 - BUMN Psychotest Module
- DISC module
- IST module
- result interpretation
- score mapping

## Phase 6 - Advanced Psychotest Module
- Kraepelin module
- Papikostick module
- profile report
- analytics comparison

## Phase 7 - Platform Enhancement
- coupon/referral
- leaderboard
- notifications
- gamification
- affiliate/institution package

---

## 14. Billing & Access Rules

1. User harus login untuk membeli paket.
2. User hanya bisa mengakses tes yang termasuk dalam paket aktif.
3. Satu paket bisa membuka beberapa program dan test type.
4. Paket dapat memiliki:
   - durasi akses
   - limit attempt
   - akses pembahasan
   - akses report premium
5. Setelah subscription berakhir, akses premium ditutup.

---

## 15. Admin Workflow

### 15.1 Content Workflow
- admin membuat program
- admin membuat test type
- admin membuat bank soal/form
- admin menyusun paket latihan
- admin publish paket

### 15.2 User Workflow
- user register
- user pilih paket
- user bayar
- sistem aktifkan subscription
- user mengerjakan latihan
- sistem menyimpan hasil & progress

---

## 16. Technical Recommendations for Laravel Stack

## 16.1 Backend
- Laravel 12.x
- MySQL 8+
- Redis untuk cache/queue
- Laravel Horizon jika memakai queue intensif

## 16.2 Frontend
- Inertia.js
- Vue 3
- Tailwind CSS
- Pinia untuk state management jika diperlukan

## 16.3 Admin
- Inertia admin custom, atau
- Filament untuk percepat backoffice

Jika fokus cepat go-live, **Filament untuk admin panel** sangat direkomendasikan.

---

## 17. Risk & Complexity Notes

### Risiko Teknis
- scoring psikotes tidak bisa disamakan semua
- Papikostick butuh desain scoring yang hati-hati
- Kraepelin butuh UI yang sangat interaktif
- randomisasi soal harus konsisten
- result interpretation harus tervalidasi

### Risiko Produk
- scope terlalu besar jika semua tes dibuat sekaligus
- user CPNS dan BUMN punya behavior berbeda
- pricing package harus dipisah dengan jelas

---

## 18. Rekomendasi Next Step

Urutan kerja yang saya sarankan:

1. finalisasi **implementation plan**
2. desain **high-level ERD**
3. susun **database schema v2**
4. pisahkan:
   - generic tables
   - CPNS-specific tables
   - BUMN psychotest-specific tables
5. buat **migration plan Laravel**
6. buat **admin feature backlog**
7. buat **UI sitemap**

---

## 19. Kesimpulan Arsitektur

Dengan tambahan kebutuhan BUMN, sistem sebaiknya tidak lagi diposisikan hanya sebagai:
- "platform latihan CPNS"

tetapi menjadi:
- **platform SaaS latihan seleksi CPNS, BUMN, dan psikotes kerja**

Arsitektur terbaik adalah:
- **modular monolith**
- **generic engine + specialized test modules**
- **subscription-based SaaS**
- **role-based admin management**

Dengan pendekatan ini, Anda bisa:
- mulai dari CPNS + BUMN
- lalu menambah modul baru tanpa ubah struktur besar
- menjaga codebase tetap rapi untuk Laravel + Inertia + Vue

---

## 20. Deliverables Setelah Dokumen Ini

Setelah implementation plan ini, dokumen berikutnya yang sebaiknya dibuat:

1. `database-schema-saas-cpns-bumn.md`
2. `erd-saas-cpns-bumn.md`
3. `laravel-migration-plan.md`
4. `feature-backlog-mvp.md`
