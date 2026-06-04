# e-Voting System — Mzumbe University

A full-featured web-based election platform for the Mzumbe University Student Union. Students self-register with their personal details, await faculty-level approval, then vote securely behind OTP two-factor authentication. Administrators manage the full election lifecycle from candidate setup through to certified result distribution via email.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.2) |
| Auth & Permissions | Spatie Laravel Permission v6 |
| Frontend | Bootstrap 4, Bootstrap Icons, vanilla JS (Fetch API / AJAX) |
| Database | MySQL / MariaDB 10.4+ |
| Sessions | Database-backed |
| Email | Laravel Mail (SMTP / Mailpit / Log driver) |

---

## Roles & Landing Pages

| Role | Permissions | Landing Page |
|---|---|---|
| `admin` | `manage_users` + `manage_election` | `/dashboard` |
| `election_admin` | `manage_election` | `/election` |
| `voter` | `vote` | OTP verification → `/voter` |

---

## Faculties & Programmes

| Faculty | Programmes |
|---|---|
| Faculty of Science and Technology | BSc ICT with Business, BSc ICT with Management, BSc Information Technology Systems |
| Faculty of Social Sciences | BA Sociology, BA Political Science and Public Administration, BA Development Studies, BA Communication and Media Studies |
| School of Business | Bachelor of Accounting and Finance, Bachelor of Entrepreneurship and Innovation Management, Bachelor of Business Administration and Marketing |
| School of Law | LLB Law, LLB Law with International Relations |
| School of Public Administration and Management | BA Public Administration, BA Local Government Administration, BA Human Resource Management |

---

## Two-Email System

Each voter has two separate email addresses:

| Type | Format | Purpose |
|---|---|---|
| **Login email** (system-generated) | `firstname.lastname.YEAR@mzumbeuniversity.com` | Used only for signing in |
| **Personal email** (student-provided) | e.g. `john@gmail.com` | Receives OTPs, credentials, and results |

---

## Feature Overview

### Voter Self-Registration (`/register/voter`)
- Student provides: full name, **personal email**, registration number (must contain enrolment year), programme, passport photo
- Faculty auto-fills from selected programme
- System generates the login email automatically
- Application queued as **pending** — no access until approved

### Election Admin Approval
- Each election admin is assigned to a **specific faculty**
- Pending Registrations panel in Election Control Centre shows only their faculty's queue
- **Approve** → creates user account + student profile, emails login credentials to student's personal email
- **Reject** → optional reason recorded

### OTP Two-Factor Login (voters only)
1. Voter submits login email + password
2. System validates, sends 6-digit OTP to **personal email** (10-min expiry)
3. Voter enters OTP on dedicated page — only fully authenticated on correct code
4. Admins and election admins bypass OTP

### Admin Console (`/dashboard`)
- Manage faculties, programmes, and candidates (full CRUD)
- Manage users — create admins, election admins, voters; assign faculty to election admins

### Election Control Centre (`/election`)
- **Timeline settings** — set voting open/close datetime and acceptance deadline
- **Pending voter registrations** — approve or reject with one click
- **Live activity log** — anonymised vote feed (hashed voter ID, faculty, position, partial IP), auto-refreshes every 5 s
- **Release Results** — calculates winners per position, emails each candidate at their personal email with result + acceptance link
- **Candidate Acceptances** — verify each candidate's response
- **Send Results to Voters** — emails final certified results to all voters' personal emails

### Voter Portal (`/voter`)
- Filtered ballot — only sees candidates relevant to their faculty/programme
- Live countdown to voting deadline
- Review step before confirming — one-way submission
- Duplicate-vote prevention and timeline enforcement server-side

### Candidate Acceptance Workflow
1. Admin releases results → candidates emailed with result + signed token link
2. Candidate opens `/acceptance/{token}` → accepts or declines
3. Election admin verifies in the control centre
4. Admin sends results email to all voters

---

## Security Notes

- OTP two-factor for all voter logins
- Voter identity hashed in audit logs (SHA-256 + APP\_KEY) — ballot secrecy preserved
- Duplicate votes blocked per student per position at DB level
- Voting window strictly enforced server-side
- Acceptance links are single-use, token-gated (no login required)

---

## Quick Start

See [DEVELOPMENT.md](DEVELOPMENT.md) for full installation and setup instructions.

---

## License

MIT
