# e-Voting System

A full-featured web-based election platform for university student organisation elections. Administrators manage candidates, faculties, programs, and voters. Voters cast secret ballots through a secure, role-gated interface. Results are automatically distributed via email with a candidate acceptance workflow.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.2) |
| Auth & Permissions | Spatie Laravel Permission v6 |
| Frontend | Bootstrap 4, Bootstrap Icons, vanilla JS (Fetch API / AJAX) |
| Database | MySQL / MariaDB |
| Sessions | Database-backed |
| Email | Laravel Mail (SMTP / Mailpit) |

## Roles & Access

| Role | Permissions | Default Landing Page |
|---|---|---|
| `admin` | `manage_users` + `manage_election` | `/dashboard` |
| `election_admin` | `manage_election` | `/election` |
| `voter` | `vote` | `/voter` |

## Features

### Admin Console (`/dashboard`)
- Manage faculties, programs, candidates (full CRUD)
- Manage users — create voters, election admins, and admins

### Election Control Centre (`/election`) — admin & election_admin
- **Timeline settings** — set voting open/close datetime and acceptance deadline
- **Live activity log** — real-time feed of anonymised vote events (voter hash, faculty, position, IP prefix), polled every 5 s
- **Release Results** — calculates winners per position, emails every candidate with result + acceptance link
- **Candidate Acceptances** — table showing each candidate's response; election admin verifies responses
- **Send Results to Voters** — emails final certified results to all registered voters

### Voter Portal (`/voter`)
- Sees only candidates relevant to their faculty/program:
  - **President** — all voters
  - **Faculty Rep / Senator** — voters in that faculty
  - **Class Rep** — voters in that program
- Live countdown to voting deadline
- Select one candidate per eligible position
- **Review page** — confirm all selections before submitting (ballot locked on confirm)
- Duplicate-vote prevention enforced server-side
- Election timeline enforced (cannot vote outside open window)

### Candidate Acceptance Workflow
1. Admin releases results → candidates emailed with result + signed link
2. Candidate opens `/acceptance/{token}` → accepts or declines
3. Election admin verifies each response in the control centre
4. Admin sends results email to all voters

### Security
- All votes hashed to prevent ballot linkage
- Voter identity hashed in audit logs (SHA-256 + APP_KEY)
- Duplicate votes blocked per student per position
- Voting window strictly enforced server-side
- Acceptance links are single-use token-gated (no authentication required)

## Quick Start

See [DEVELOPMENT.md](DEVELOPMENT.md) for full installation and setup instructions.

## License

MIT
