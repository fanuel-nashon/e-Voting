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
php artisan optimize:clear               # clear config, route, view, and app caches
php artisan permission:cache-reset       # clear Spatie permission cache (run after role changes)
php artisan migrate:fresh --seed         # full reset — drops all tables and reseeds
php artisan storage:link                 # link storage/app/public → public/storage (voter photos)
php artisan db:seed --class=UsersSeeder  # re-run only the admin accounts seeder
php artisan db:seed --class=DemoSeeder   # create demo voters and open election window
php artisan demo:vote                    # simulate real-time voting (requires DemoSeeder first)
php artisan demo:vote --reset            # wipe votes/logs, then simulate again
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
| GET | `/reports` | Voting reports dashboard (4 tabs) |
| GET | `/reports/export?type=overall` | Download overall election CSV report |
| GET | `/reports/export?type=positions` | Download per-position CSV report |
| GET | `/reports/export?type=faculties` | Download per-faculty participation CSV report |
| GET | `/reports/export?type=candidates` | Download all-candidates CSV report |

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

### 10. View reports
Go to `/reports` (accessible from the sidebar on any admin page).
- **Overview** tab shows overall participation stats and charts.
- **By Position** tab shows ranked results for every position.
- **By Faculty** tab shows turnout broken down by faculty.
- **All Candidates** tab shows a searchable league table across all candidates.
- Use the Export buttons on the Overview tab to download any report as CSV.

---

## Demo Mode

The demo factory lets you showcase the full election lifecycle — live activity log, real-time vote accumulation, and post-election reporting — without real voters or email configuration.

### Setup (one time)

```bash
php artisan migrate:fresh --seed          # full database reset with all base data
php artisan db:seed --class=DemoSeeder   # add 80 demo voters + open the election window
```

`DemoSeeder` is idempotent — re-running it skips accounts that already exist and resets the election window to open now, closing in 4 hours.

### Run the simulation

```bash
php artisan demo:vote
```

Open `/election` in the browser **before** running this. Votes appear in the live activity log every ~0.5 s. When it finishes, open `/reports` to see the full statistical report with charts.

### `demo:vote` options

| Option | Default | Effect |
|---|---|---|
| `--speed=fast` | — | ~0.1 s avg between voters — 80 voters finish in ~8 s |
| `--speed=normal` | ✔ | ~0.5 s avg — good pace for live presentations |
| `--speed=slow` | — | ~2.5 s avg — draw it out for long demos |
| `--turnout=N` | `78` | Percentage of voters that participate (1–100) |
| `--voters=N` | `0` | Hard cap on voters simulated; `0` uses `--turnout` |
| `--reset` | — | Wipes all votes and vote_logs first, then runs |

### Re-running a demo

```bash
php artisan demo:vote --reset            # clear votes, simulate again (same voters)
php artisan demo:vote --reset --speed=fast   # fast reset + fast simulation
```

To start completely fresh (drop all data including demo voters):

```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoSeeder
php artisan demo:vote
```

### Demo account credentials

| Pattern | Example | Password |
|---|---|---|
| `demo.voter.N@mzumbeuniversity.com` | `demo.voter.1@mzumbeuniversity.com` | `demo1234` |

Demo accounts have realistic Tanzanian names and are spread evenly across all programmes and faculties. Their registration numbers follow the format `DEMO/PROG/2024/NNN`. These accounts are clearly distinguishable from real voter accounts (`std####@students.university.ac.tz`) in the Users panel.

### What each speed setting looks like in the browser

| Speed | Voters per minute | Live log behaviour |
|---|---|---|
| `fast` | ~600 | Log fills in bursts; good for a quick "see it work" moment |
| `normal` | ~120 | Steady stream of entries — ideal for a watched live demo |
| `slow` | ~24 | Sparse, deliberate entries — useful if talking through each vote |

### Voting weight distribution

Candidates are assigned weighted probabilities so results look realistic rather than uniform. Within each position the candidate seeded first receives approximately 42% of votes, the second ~28%, the third ~16%, and so on. Combined with a ~12% per-position abstention rate, this reliably produces a clear winner, a competitive runner-up, and a realistic participation curve across faculties.

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
