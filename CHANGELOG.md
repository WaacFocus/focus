# Focus — Changelog

All notable changes to this project are documented here, grouped by version.

---

## v1.07 — 2026-06-19

### Added
- **Custom Report Builder** — build and save custom reports under Reports > Custom Reports
  - Pick a data source (Clients, Jobs, Tasks, Renewals)
  - Choose columns, add filters (field / operator / value), set sort direction
  - Live AJAX preview before saving
  - Save and name reports; reload saved config for editing
  - Export saved reports as CSV, PDF (portrait or landscape), or print
  - Email saved report to selected users via SMTP2GO
  - Custom Reports link added to Insights section of sidebar (desktop + mobile)

---

## v1.06 — 2026-06-19

### Added
- **Quarterly frequency** — jobs can now be scheduled as Quarterly (due date advances +3 months on completion)
- **Version number** — displayed at the bottom of both desktop and mobile sidebars (`config/version.php`)

---

## v1.05 — 2026-06-19

### Added
- **Two-factor authentication (2FA)**
  - TOTP setup and confirmation via `pragmarx/google2fa`
  - Admin can set up / disable TOTP for any user from the Users panel
  - Passkey (WebAuthn) registration via `laragear/webauthn`
  - 2FA challenge screen on login
- **Profile — My Details**
  - Password change page (current / new / confirm, with show/hide toggles)
  - Security page (manage TOTP and passkeys)
  - My Details section pinned to bottom of sidebar for all users
- **Dashboard — job filters**
  - Collapsible filter bar on "My Jobs" section: Status, Frequency, Client, Due from/to
  - Filter button shows "On" badge when active filters are in use
- **Jobs index — inline status dropdown**
  - Status column replaced with coloured `<select>` that patches via AJAX
  - Toast notification for recurring jobs when marked complete
- **Column order persistence**
  - Drag-to-reorder table columns saved server-side in `users.preferences` JSON column
  - Order restored on next login / new browser

### Fixed
- Dashboard job complete button spinner remaining after error response
- Authenticator app showing "Laravel" instead of "Focus" (`APP_NAME=Focus` in `.env`)
- In-progress badge colour mismatch between dashboard and jobs page (unified to Bootstrap blue `#cfe2ff` / `#084298`)
- Mobile sidebar menu links not navigating (removed `data-bs-dismiss` from `<a>` tags)

### Changed
- Site made responsive for iPad and iPhone; desktop layout unchanged
- Mobile offcanvas sidebar includes Focus logo in header
- Desktop and mobile sidebars are separate elements to avoid Bootstrap `offcanvas-lg` background override

---

## v1.04 and earlier

Initial build — clients, jobs, tasks, projects, renewals, services, products, reports (Fixed Prices, Upcoming Jobs), user management, activity log, Companies House integration, SMTP2GO email delivery.
