# Development Guide

Complete local setup, seeding, testing, and troubleshooting guide for the Mzumbe University e-Voting system.

---

## Prerequisites

| Tool | Minimum version | Check |
|---|---|---|
| PHP | 8.2 | `php --version` |
| Composer | 2.x | `composer --version` |
| MySQL / MariaDB | 10.4+ | `mysql --version` |
| Git | any | `git --version` |

> **Windows:** [Laragon](https://laragon.org/) bundles PHP, MariaDB, and a local mail server in one portable install.

---

## 1. Clone & Install

```bash
git clone <repository-url> e-voting
cd e-voting
composer install
```

---

## 2. Environment Configuration

```bash
cp .env.example .env       # Linux/macOS
copy .env.example .env     # Windows
php artisan key:generate
```

Key values in `.env`:

```env
APP_NAME="Mzumbe University e-Voting"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_voting
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database

# Development — writes all emails to storage/logs/laravel.log
MAIL_MAILER=log

# For real sending (Mailpit on port 1025 or SMTP):
# MAIL_MAILER=smtp
# MAIL_HOST=127.0.0.1
# MAIL_PORT=1025
# MAIL_FROM_ADDRESS="noreply@mzumbeuniversity.com"
# MAIL_FROM_NAME="Mzumbe University e-Voting"
```

---

## 3. Create Database

```sql
CREATE DATABASE e_voting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 4. First-time Setup (all-in-one)

```bash
php artisan migrate:fresh --seed   # drop, rebuild, seed
php artisan storage:link           # expose uploaded photos publicly
php artisan permission:cache-reset # clear Spatie permission cache
```

> Use `php artisan migrate` instead of `migrate:fresh` if you want to preserve existing data and only apply new migrations.

---

## 5. Start the Server

```bash
php artisan serve
# or on a specific port:
php artisan serve --port=8005
```

---

## 6. Default Seeded Accounts

| Email | Password | Role | Notes |
|---|---|---|---|
| `admin@gmail.com` | `Password_123` | admin | Full access |
| `electionadmin@gmail.com` | `Password_123` | election_admin | Needs faculty assigned (see below) |
| `std0001@students.university.ac.tz` | `student123` | voter | Seeded student (no personal email) |

### Assign faculty to the election admin (required for approval workflow)

```bash
php artisan tinker
```
```php
$u = \App\Models\User::where('email','electionadmin@gmail.com')->first();
$f = \App\Models\Faculty::where('name','Faculty of Science and Technology')->first();
$u->update(['faculty_id' => $f->id]);
```

Or go to **Admin Console → Users / Voters → Set Faculty** next to the election admin account.

---

## 7. Useful Commands

```bash
php artisan optimize:clear          # clear config, route, view, and app caches
php artisan permission:cache-reset  # clear Spatie permission cache (run after role changes)
php artisan migrate:fresh --seed    # full reset — drops all tables and reseeds
php artisan storage:link            # link storage/app/public → public/storage (voter photos)
php artisan db:seed --class=UsersSeeder  # re-run only the admin accounts seeder
```

---

## Routes Reference

### Guest (no login required)

| Method | URL | Description |
|---|---|---|
| GET | `/` | Login page |
| POST | `/login.submit` | Authenticate user |
| GET | `/register/voter` | Student voter self-registration form |
| POST | `/register/voter` | Submit registration (with photo upload) |
| GET | `/voter/otp` | OTP entry page (session-gated) |
| POST | `/voter/otp` | Verify OTP and complete voter login |
| GET | `/password/reset` | Forgot password form |
| POST | `/reset-password` | Send 6-digit reset token |
| GET | `/enter-token` | Enter token + set new password |
| POST | `/change-password` | Verify token / update password |
| GET | `/acceptance/{token}` | Candidate result acceptance form |
| POST | `/acceptance/{token}` | Submit acceptance response |

### Voter (`vote` permission)

| Method | URL | Description |
|---|---|---|
| GET | `/voter` | Ballot dashboard |
| POST | `/voter/review` | Preview ballot before submitting |
| POST | `/voter/confirm` | Cast votes (one-way) |
| GET | `/voter/done` | Vote confirmed page |

### Election Management (`manage_election`)

| Method | URL | Description |
|---|---|---|
| GET | `/dashboard` | Admin console (faculties, programmes, candidates) |
| GET | `/election` | Election Control Centre |
| POST | `/election/timeline` | Save voting open/close window |
| GET | `/election/logs` | Poll live vote log (JSON, every 5 s) |
| GET | `/election/stats` | Poll live participation stats (JSON) |
| POST | `/election/release` | Release results + email all candidates |
| POST | `/election/acceptances/{id}/verify` | Verify a candidate acceptance |
| POST | `/election/publish` | Email final results to all voters |
| GET | `/voter-registrations/pending` | List pending registrations (JSON) |
| POST | `/voter-registrations/{id}/approve` | Approve + create account + email credentials |
| POST | `/voter-registrations/{id}/reject` | Reject with optional reason |

### User Management (`manage_users`)

| Method | URL | Description |
|---|---|---|
| GET | `/users` | Users & voters management page |
| POST | `/users` | Create user with role (+ faculty for election_admin) |
| PATCH | `/users/{id}/faculty` | Assign / change faculty for election admin |
| DELETE | `/users/{id}` | Delete user |

---

## Database Schema

```
users
  id, name, email (login — system-generated), personal_email (real — for all mail),
  password, faculty_id (FK → faculties, for election_admins), remember_token

students
  id, reg_no, name, faculty_id, program_id, user_id (FK → users)

voter_registrations
  id, name, email (generated login), personal_email (real),
  reg_number, reg_year, program_id, faculty_id, photo,
  status (pending|approved|rejected), processed_by, processed_at, rejection_reason

otp_tokens
  id, user_id, token (6 digits), expires_at, used_at

faculties         id, name
programs          id, name, faculty_id
positions         id, name, type (president|faculty_rep|senator|class_rep), faculty_id, program_id
candidates        id, name, image, position_id
votes             id, student_id, candidate_id, position_id

vote_logs
  id, voter_hash (SHA-256), faculty_name, program_name, position_name,
  action, ip_prefix (first two octets only), metadata, created_at

election_settings
  id, title, voting_opens_at, voting_closes_at, results_released_at, acceptance_deadline_at

candidate_acceptances
  id, candidate_id, position_id, votes_received, won, token,
  notification_sent_at, responded_at, accepted, response_note,
  verified_at, verified_by

sessions / cache / jobs / Spatie permission tables
```

---

## End-to-End Test Flow

### 1. Set election timeline
Log in as `electionadmin@gmail.com` → `/election` → set Opening and Closing datetimes → Save.

### 2. Register a voter
Open `/register/voter` → fill name, **personal email** (a real or test inbox), registration number (must contain a 4-digit year e.g. `MZ/ICT/2022/001`), programme, photo → Submit.

### 3. Approve the registration
Log in as election admin → `/election` → **Pending Voter Registrations** → Approve.
Check the student's personal email (or `storage/logs/laravel.log` with `MAIL_MAILER=log`) for login credentials.

### 4. Voter OTP login
Use the emailed login email + password on the login page. An OTP is sent to the student's **personal email**. Enter it on the OTP page to complete login.

### 5. Cast vote
Select candidates for each eligible position → Review Ballot → Confirm.

### 6. Watch live logs
Open `/election` as election admin — new entries appear every 5 s showing anonymised voter activity.

### 7. Release results
After `voting_closes_at` passes → **Release Results** → candidates receive results + acceptance link at their personal email.

### 8. Candidate acceptance
Open `/acceptance/{token}` → submit Accept or Decline response.

### 9. Verify & publish
In `/election` → verify each acceptance → **Send Results to All Voters** → all voters receive results at their personal email.

---

## Email Address System

| Address type | Example | Used for |
|---|---|---|
| Login email (generated) | `john.doe.2022@mzumbeuniversity.com` | Signing in only |
| Personal email (student-provided) | `john.doe@gmail.com` | OTP, credentials, results |

Year is parsed automatically from the registration number (first 4-digit sequence found).
Duplicate login emails get a numeric suffix: `john.doe.2022.2@mzumbeuniversity.com`.

---

## Common Issues

| Problem | Fix |
|---|---|
| `Specified key was too long (767 bytes)` | Already fixed via `Schema::defaultStringLength(191)` in `AppServiceProvider` |
| `Route [X] not defined` | `php artisan route:clear` |
| `403 on dashboard` | `php artisan db:seed` then `php artisan permission:cache-reset` |
| Voter photos not showing | `php artisan storage:link` |
| OTP page blank / session lost | Go back to `/` and log in again |
| Emails not arriving | Set `MAIL_MAILER=log` — check `storage/logs/laravel.log` |
| Election admin sees no pending registrations | Assign a faculty to the election admin account |
