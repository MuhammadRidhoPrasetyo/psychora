# Feature Backlog MVP

> Backlog fitur MVP untuk SaaS latihan tes CPNS & BUMN.
> Prioritas dibagi menjadi:
> - P0 = wajib untuk launch awal
> - P1 = penting setelah core stabil
> - P2 = enhancement

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
- DISC module
- IST module
- admin CRUD soal
- hasil test dasar

## Out of Scope Sementara
- mobile app native
- leaderboard kompleks
- AI recommendation
- affiliate/referral
- institution dashboard
- proctoring canggih
- full Papikostick report psikologi profesional

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
- Test type listing
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
- Category-based scoring
- Explanation per question
- CPNS tryout result summary

### P1
- Passing grade simulation
- National-style ranking mockup
- Package bundling by CPNS level

---

## Epic H - DISC Module

### P0
- DISC form management
- DISC question management
- Most/least answer UI
- DISC scoring
- DISC result summary

### P1
- Profile interpretation detail
- DISC chart visualization

---

## Epic I - IST Module

### P0
- IST form management
- 9 subtest configuration
- Sequential subtest flow
- Subtest timer
- IST scoring per category
- Basic IQ-style result summary

### P1
- Randomized subtest questions
- Detailed interpretation per category

---

## Epic J - Admin Panel

### P0
- Admin login
- Manage users
- Manage plans
- Manage programs
- Manage test types
- Manage tests
- Manage sections
- Manage questions
- Manage options
- Publish/unpublish tests
- View user attempts
- View payments

### P1
- Dashboard analytics
- Bulk import question bank
- Export results

---

## Epic K - Reporting

### P0
- User result history
- Result detail page
- Admin result viewer

### P1
- Score trend chart
- Compare attempts
- Export PDF

---

## Epic L - Security & Logging

### P0
- Auth middleware
- Subscription access guard
- Attempt access validation
- Basic activity logs

### P1
- IP/device logging
- suspicious behavior logs
- rate limiting test start

---

# 4. Sprint Recommendation

## Sprint 1
- auth
- landing page
- core database
- admin foundation

## Sprint 2
- subscription plans
- payments basic
- dashboard user
- catalog tests

## Sprint 3
- generic test engine
- questions/options
- scoring objective

## Sprint 4
- CPNS module
- result page
- explanation page

## Sprint 5
- DISC module
- admin management DISC

## Sprint 6
- IST module
- subtest flow
- scoring

## Sprint 7
- logs
- reporting basic
- polish UI/UX

---

# 5. Definition of Done MVP

MVP dianggap selesai jika:
- user dapat registrasi dan login
- user dapat membeli minimal 1 paket
- admin dapat mengaktifkan subscription
- user dapat mengakses latihan sesuai entitlement
- user dapat mengerjakan CPNS test
- user dapat mengerjakan DISC test
- user dapat mengerjakan IST test
- skor dan hasil tampil dengan benar
- admin dapat mengelola bank soal dasar

---

# 6. Post-MVP Roadmap

## Phase 2
- Kraepelin module
- Papikostick module
- payment gateway full automation
- analytics lebih detail

## Phase 3
- gamification
- leaderboard
- referral
- institution package
- mobile responsiveness improvement
- advanced reporting

---

# 7. Notes

## Rekomendasi MVP bisnis
Jika ingin cepat validasi pasar:
- launch dulu dengan **CPNS + DISC + IST**
- Kraepelin dan Papikostick masuk gelombang berikutnya

## Alasan
- lebih cepat release
- effort UI/scoring lebih rendah
- bisa mulai mendapatkan feedback user lebih awal