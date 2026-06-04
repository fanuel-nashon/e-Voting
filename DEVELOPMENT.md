# Development Guide

Complete setup, seeding, and testing guide for the e-Voting system.

---

## Prerequisites

| Tool | Minimum version | Check |
|---|---|---|
| PHP | 8.2 | `php --version` |
| Composer | 2.x | `composer --version` |
| MySQL / MariaDB | 10.4+ | `mysql --version` |
| Git | any | `git --version` |

> **Windows users:** [Laragon](https://laragon.org/) provides PHP, MariaDB, and a mail catcher in one portable install.

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
cp .env.example .env        # Linux / macOS
copy .env.example .env      # Windows
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="e-Voting System"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_voting
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database

# For local development — emails written to storage/logs/laravel.log
MAIL_MAILER=log

# For real email (SMTP / Mailpit):
# MAIL_MAILER=smtp
# MAIL_HOST=127.0.0.1
# MAIL_PORT=1025
# MAIL_FROM_ADDRESS="noreply@evoting.local"
# MAIL_FROM_NAME="e-Voting System"
```

> **Mail during development:** Set `MAIL_MAILER=log` to capture all emails in `storage/logs/laravel.log`. Switch to `smtp` + [Mailpit](https://github.com/axllent/mailpit) to view emails in a browser (port 8025). The password-reset and results-notification flows require mail to be working.

---

## 3. Create the Database

```sql
CREATE DATABASE e_voting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 4. Run Migrations

```bash
php artisan migrate
```

> **Note:** With `SESSION_DRIVER=database`, the sessions table is created automatically during boot. The first migration handles this with `dropIfExists` — migrations always complete cleanly on a fresh database.

---

## 5. Seed the Database

```bash
php artisan db:seed
```

This runs all seeders in order:

| Seeder | What it creates |
|---|---|
| `PermissionSeeder` | 3 permissions: `manage_users`, `manage_election`, `vote` |
| `RoleSeeder` | 3 roles with permissions assigned |
| `FacultySeeder` | 5 faculties |
| `ProgramSeeder` | ~17 programs (3–4 per faculty) |
| `PositionSeeder` | 1 president + 2 per faculty (rep + senator) + 1 class rep per program |
| `StudentSeeder` | 6 students per program, each with a voter user account (password: `student123`) |
| `CandidateSeeder` | 2–3 candidates per position |

---

## 6. Create Your Admin Account

```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name'     => 'Admin',
    'email'    => 'admin@example.com',
    'password' => bcrypt('password'),
]);
$user->assignRole('admin');
exit
```

Run the permission cache reset after any role/permission changes:

```bash
php artisan permission:cache-reset
```

---

## 7. Start the Server

```bash
php artisan serve
# or on a custom port:
php artisan serve --port=8005
```

---

## 8. Clear Caches

```bash
php artisan optimize:clear
php artisan permission:cache-reset
```

---

## Routes Reference

### Guest
| Method | URL | Description |
|---|---|---|
| GET | `/` | Login page |
| POST | `/login.submit` | Authenticate |
| GET | `/password/reset` | Forgot password |
| POST | `/reset-password` | Send 6-digit token |
| GET | `/enter-token` | Enter token + set password |
| POST | `/change-password` | Verify token / update password |
| GET | `/acceptance/{token}` | Candidate acceptance form |
| POST | `/acceptance/{token}` | Submit acceptance |

### Voter (`vote` permission)
| Method | URL | Description |
|---|---|---|
| GET | `/voter` | Voter ballot dashboard |
| POST | `/voter/review` | Preview ballot selections |
| POST | `/voter/confirm` | Confirm and cast votes |
| GET | `/voter/done` | Vote confirmed page |

### Election Management (`manage_election`)
| Method | URL | Description |
|---|---|---|
| GET | `/dashboard` | Admin console |
| GET/POST/PUT/DELETE | `/faculties` | Faculty CRUD |
| GET/POST/PUT/DELETE | `/programs` | Program CRUD |
| GET/POST/PUT/DELETE | `/candidates` | Candidate CRUD |
| GET | `/election` | Election control centre |
| POST | `/election/timeline` | Save voting timeline |
| GET | `/election/logs` | Poll live vote logs (JSON) |
| GET | `/election/stats` | Poll live stats (JSON) |
| POST | `/election/release` | Release results + email candidates |
| POST | `/election/acceptances/{id}/verify` | Verify acceptance |
| POST | `/election/publish` | Email results to all voters |

### User Management (`manage_users`)
| Method | URL | Description |
|---|---|---|
| GET | `/users` | Users & voters page |
| POST | `/users` | Create user with role |
| DELETE | `/users/{id}` | Delete user |

---

## Database Schema

```
users                   id, name, email, password
students                id, reg_no, name, faculty_id, program_id, user_id (FK → users)
faculties               id, name
programs                id, name, faculty_id
positions               id, name, type (president|faculty_rep|senator|class_rep), faculty_id, program_id
candidates              id, name, image, position_id
votes                   id, student_id, candidate_id, position_id
vote_logs               id, voter_hash, faculty_name, program_name, position_name, action, ip_prefix, created_at
election_settings       id, title, voting_opens_at, voting_closes_at, results_released_at, acceptance_deadline_at
candidate_acceptances   id, candidate_id, position_id, votes_received, won, token, responded_at, accepted, verified_at
sessions / cache / jobs Laravel internals
Spatie permission tables
```

---

## Testing the Full Election Flow

### 1. Set up the election timeline
Log in as admin → navigate to **Election Control Centre** (`/election`) → set opening/closing times.

### 2. Test voter login
Use any seeded student account (password: `student123`). Find emails in the database:
```bash
php artisan tinker
>>> \App\Models\User::role('voter')->pluck('email')->take(5);
```

### 3. Cast a vote
Log in as a voter → select candidates → review → confirm.

### 4. Watch the live log
Open `/election` as admin/election_admin — new entries appear every 5 seconds after votes are cast.

### 5. Release results
After voting_closes_at has passed → click **Release Results**. With `MAIL_MAILER=log`, check `storage/logs/laravel.log` for the candidate emails.

### 6. Submit acceptance
Copy the acceptance token from the log, open `/acceptance/{token}` and submit a response.

### 7. Verify & publish
In the Election Control Centre → verify the acceptance → click **Send Results to Voters**.

---

## Common Issues

### `Table 'sessions' already exists`
```bash
# Already handled by the migration. If it recurs, clear the cache first:
php artisan optimize:clear
php artisan migrate
```

### `Route [logout] not defined` / `Route [X] not defined`
```bash
php artisan route:clear
```

### 403 on dashboard after login
```bash
php artisan db:seed
php artisan tinker
>>> \App\Models\User::where('email','you@example.com')->first()->assignRole('admin');
php artisan permission:cache-reset
```

### Voter sees "no profile linked" message
The voter user has no associated `students` record. Either run `db:seed` (which creates student profiles) or manually link one:
```php
\App\Models\Student::create([
    'reg_no' => 'STD/0001/2024', 'name' => 'John Doe',
    'faculty_id' => 1, 'program_id' => 1, 'user_id' => $voterId,
]);
```

### Emails not arriving
Set `MAIL_MAILER=log` in `.env` — all emails appear in `storage/logs/laravel.log`. Or use [Mailpit](https://github.com/axllent/mailpit) on port 1025 to receive them in a web UI.

---

## Default Seeded Credentials

| Account | Email pattern | Password | Role |
|---|---|---|---|
| Admin (manual) | `admin@example.com` | `password` | admin |
| Students/Voters | `firstname.lastnameN@students.university.ac.tz` | `student123` | voter |
