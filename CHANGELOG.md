# Focus — Changelog

All notable changes to this project are documented here, grouped by version.

---

## v1.23 — 2026-06-29

### Added
- **Main contact selection** — radio button per officer in the confirmation modal; selecting one populates the company's contact first/last name fields automatically
- **Self Assessment flag** — SA checkbox per officer; stored on the director record (shown as "SA" badge on client detail page); if the officer is also created as a client, the "Self Assessment" service is automatically attached if it exists in the services list

---

## v1.22 — 2026-06-29

### Added
- **Create officers as clients** — in the Companies House confirmation modal, each officer has a "Create as client?" checkbox; ticking it reveals a client code field; on confirm those officers are also created as individual prospect client records with first/last name split automatically

---

## v1.21 — 2026-06-29

### Added
- **CH Registered Address** stored separately from main client address — address line 1 & 2, locality, region, postcode, country all saved as distinct CH fields
- Confirmation modal now shows full registered address (all lines) before setup
- Client detail Companies House card displays formatted registered address

---

## v1.20 — 2026-06-29

### Added
- **Director / officer import from Companies House** — selecting a company now fetches active officers in parallel and shows a confirmation modal (company summary + officers table) before populating the form
- Officers are saved to a new `client_directors` table on client creation
- Client detail page shows an Officers / Directors card with name, role, appointed date, nationality, and DOB (month/year)

---

## v1.19 — 2026-06-29

### Added
- **Companies House data section on clients** — incorporated date, CH status, jurisdiction, SIC codes, accounts year end, accounts due, confirmation statement due
- Companies House lookup now auto-populates all CH fields on company select; re-sync available in edit mode
- Client detail page shows Companies House card with overdue/due-soon alerts on key dates

---

## v1.18 — 2026-06-29

### Removed
- **Payroll section removed from clients** — `payroll_fpa`, `payroll_billing_interval`, `sa_billed_separately`, and `payroll_invoiced_separately` fields dropped from database, client forms, client detail view, reports, PDF export, CSV export, email report, and backup import/export

---

## v1.17 — 2026-06-29

### Changed
- Engagement Letters nav icon updated to renewal/repeat symbol

---

## v1.16 — 2026-06-29

### Added
- **Admin → Letter Sections** — manage default wording for all engagement letter sections
  - Add, edit, and delete sections; drag-to-reorder default sequence
  - Per-section flags: Active (shown in builder), Pre-ticked (auto-selected on new letters), Mandatory (cannot be removed)
- **Mandatory sections** — Introduction, Our Responsibilities, Client Responsibilities, Confidentiality & Data Protection, and Acceptance of Terms are locked in every new letter; configurable from admin
- **View button on builder** — chevron button on each section row expands/collapses wording for editing without affecting the tick/include state

### Changed
- Removed old Renewals nav link; Engagement Letters now a top-level nav item pointing directly to the letter index
- Builder checkbox and wording preview are now fully independent — ticking a section no longer opens the wording panel

---

## v1.15 — 2026-06-28

### Added
- **Engagement Letter Builder** — compose professional engagement letters from selectable template sections
  - 13 pre-written sections (Introduction, Annual Accounts, Corporation Tax, Self Assessment, VAT, Payroll, Bookkeeping, Company Secretarial, Fees, Client Responsibilities, Confidentiality, Acceptance, and more)
  - Drag-to-reorder sections using SortableJS; tick to include/exclude
  - Inline content editing per section
  - Save as draft or send directly to client
- **Client signing portal** — public `/sign/{token}` page (no login required)
  - Displays the full letter; client enters full name and confirms agreement
  - Records IP address, timestamp, and signer name
- **Post-signing automation**
  - Signed PDF generated (DomPDF) with digital signature block
  - Client emailed a copy of the signed letter as a PDF attachment
  - Staff notification email sent with signer details and link to view in Focus
  - Linked renewal automatically updated to Signed with `completed_date` and next `due_date` set 12 months ahead
- **Renewals → Engagement Letters** — repurposed renewals feature for engagement letter tracking
  - Fields simplified: `completed_date`, `due_date` (always 12 months), status: pending / sent / signed / overdue
  - Removed: service link, billing cycle, amount, next renewal date
  - "Build Engagement Letter" button on renewal edit page
- **Additional billing lines** per client (monthly / quarterly / annually / one-off)
  - Managed within the client edit panel under the Fixed Price Agreement section
  - Included in GRF (Gross Recurring Fee) calculation on the Billing report
  - Annual total fee shown on the client detail page
- **GRF metric** (Gross Recurring Fee) renamed from GRR across Billing report, PDF, and email
- **Client panel improvements** — payment method changed to dropdown; SA billed separately and payroll invoiced separately toggles removed

### Changed
- Billing report card renamed from "Fixed Price Summary" to "Billing"

---

## v1.14 — 2026-06-26

### Added
- Tasks `project_id` migration included in version control (drops FK constraint, makes column nullable so tasks are fully standalone)

---

## v1.13 — 2026-06-26

### Added
- **Backup & Import** — new admin page under Admin section
  - Export any data set (Clients, Jobs, Renewals, Tasks) as a CSV file
  - Download an example/template CSV for each data set showing the required columns and accepted values
  - Import from CSV: clients matched on `client_code` (update existing or create new); jobs, renewals, and tasks always inserted as new records
  - Import summary flash message shows created / updated / skipped counts and row-level errors
- **Client Code column** — added to the Clients index table (first column, before Company)
- **Column visibility toggle** — Columns dropdown on Clients index lets you show/hide individual columns; preference saved in `localStorage`

### Changed
- **Client Code** — now a required field (red asterisk, validated client-side and server-side in both the offcanvas panel and the full create/edit forms)
- **Client Type** — now required; red asterisk added to offcanvas panel label; client-side validation fires immediately on submit before AJAX call

### Removed
- **Projects** — removed entirely: model, controller, all views, routes, and nav link; tasks are now standalone
- **Products** — removed entirely: model, controller, all views, routes, and nav link

### Fixed
- **Client edit offcanvas panel** — client type and client code fields now show red border and error message immediately on submit if left empty (previously no validation feedback appeared in the slide-out panel)
- Stale "Projects" column removed from Clients index table

---

## v1.12 — 2026-06-24

### Changed
- **Jobs index** — defaults to current user's jobs ("My Jobs"); dropdown options: My Jobs (default), All Users, or any individual user

---

## v1.11 — 2026-06-24

### Fixed
- **Dashboard My Jobs** — status filter was stacking on top of the base `whereIn` instead of replacing it; now correctly replaces the default so filtering by a specific status returns accurate results
- `assigned_to = Auth::id()` enforced on all dashboard job queries regardless of filters applied

---

## v1.10 — 2026-06-24

### Added
- **Favicon** — logo used as browser tab / bookmark icon (32×32 and 16×16 PNG, generated from logo.png)

---

## v1.09 — 2026-06-22

### Changed
- **Jobs index** — completed jobs hidden by default; status dropdown defaults to "Active Jobs" (pending + in progress only)
- Added "All (inc. completed)" option to status filter to explicitly show every status
- Dashboard My Jobs and Client detail page already excluded completed jobs (no change)

---

## v1.08 — 2026-06-19

### Added
- **Version Log** — admin page under Admin section showing full changelog with styled version/change-type cards
  - Parsed from `CHANGELOG.md` using `league/commonmark`
  - Export as PDF (DomPDF, portrait), download raw `.md` file, or print
  - Version Log link added to Admin section of sidebar (desktop + mobile)

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
