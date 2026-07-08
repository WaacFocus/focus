# Focus — Changelog

All notable changes to this project are documented here, grouped by version.

---

## v1.40 — 2026-07-08

### Fixed
- **Copy signing link** — replaced broken inline `onclick` with a proper JS handler; tries the Clipboard API first (HTTPS) and falls back to `execCommand` for HTTP environments; button turns green and shows "Copied!" briefly then restores correctly

---

## v1.39 — 2026-07-08

### Fixed
- **Timestamps now BST-aware** — app timezone changed from `UTC` to `Europe/London`; all timestamps (signing, sending, reports, emails) now display in the correct local time year-round, automatically switching between GMT and BST
- **Signed PDF timezone label** — was hardcoded as "UTC"; now uses `T` format token so it shows "GMT" or "BST" correctly

---

## v1.38 — 2026-07-08

### Fixed
- **Director letter send — Network error** — route parameter `{client}` did not match controller parameter `$directorClient`, causing Laravel model binding to fail with a 500 and an unparseable response; renamed to `$client` to align with the route

---

## v1.37 — 2026-07-08

### Fixed
- **Engagement letter service auto-tick (robust rewrite)** — matching is now done in the controller via a DB query rather than string comparison in Blade; templates are resolved to IDs using both `service_type` and `LOWER(TRIM(title))` against the client's service names, eliminating any risk of case/whitespace mismatch or Blade scope issues

---

## v1.36 — 2026-07-08

### Fixed
- **Engagement letter service auto-tick** — section matching now also compares template title (lowercased) against the client's service names, so services like "Annual Accounts" correctly tick the "Annual Accounts" section even when the template's `service_type` was seeded as `"accounts"`
- **ServiceController duplicate template prevention** — when creating a service whose name matches an existing template title, the existing template's `service_type` is updated to align rather than creating a duplicate section

---

## v1.35 — 2026-07-08

### Added
- **Director engagement letters** — after sending a company's engagement letter, if the company has directors with Self Assessment required who are registered as active clients, the system redirects to a director letters page
- **Director letters page** — lists each SA-required director client with their name, role, and email; each card has a "Send Engagement Letter" button that creates and sends a letter with all mandatory sections plus the Self Assessment section
- **Director no-email modal** — if a director has no email address, a modal prominently shows the director's name and role and prompts for their email before sending

---

## v1.34 — 2026-07-08

### Added
- **No-email prompt on engagement letter send** — if the selected client has no email address, a modal prompts for email (and optionally phone) before sending; the client record is updated via AJAX and the letter is sent immediately after

---

## v1.33 — 2026-07-08

### Added
- **Engagement letter service auto-population** — when creating a new letter from a client's page, sections whose `service_type` matches the client's assigned services are automatically pre-ticked
- **Engagement letter template sync migrations** — four migrations ensure every service has a linked letter template and every template has a linked service; core mandatory sections (Introduction, Our Responsibilities, etc.) are preserved

### Changed
- **Add Letter button** — now links to the engagement letter builder pre-populated with the client and subject rather than the old renewals letter section
- **Tasks filter** — all dropdown filters now submit immediately on change without needing to click Filter

---

## v1.32 — 2026-07-08

### Added
- **Annual Fees Breakdown on Billing report** — new section lists every annually-billed client (FPA and additional billing lines) with client code, description, and amount; total matches the Annual Fees metric card
- **Firm logo on report exports** — billing PDF and email now show the Woods logo in the header

### Changed
- **Billing report — Monthly Fees Breakdown** — renamed from "Client Breakdown"; Interval column removed; Client Code column added; section header highlighted in brand dark colour
- **Billing report — "Revenue" → "Fees"** — all labels updated: Monthly/Annual/Quarterly Fees, GRF — Gross Recurring Fees
- **Billing report exports (PDF + email)** — updated to match web layout: column widths aligned, Annual Fees Breakdown section added, Interval column removed, logo added
- **FPA Totals by Billing Interval** section removed from Billing report
- **Tasks filters** — changing any dropdown filter (User, Status, Priority, Urgent) now submits immediately without needing to click Filter
- **Director "Create as client?" column** — client code field moved to its own always-visible column so ticking Yes no longer shifts the row; directors already stored show an "Already a client" teal badge spanning both columns with a green row tint instead of a checkbox

### Removed
- **SA Job button** removed from client detail Jobs card

---

## v1.31 — 2026-07-08

### Added
- **Service auto-seeding** — creating a new service now automatically: (1) seeds a set of service-specific job statuses by copying the global defaults (Pending, In Progress, Completed), ready to customise in Admin → Job Statuses; (2) creates a starter engagement letter section for the service, ready to edit in Admin → Letter Sections
- **Dynamic category suggestions** — the Category field on the letter section form now includes all active service names as datalist suggestions alongside the built-in options

---

## v1.30 — 2026-07-08

### Added
- **Canvas / typed signature on engagement letters** — signers can now draw their signature with a finger or mouse, or switch to "Type Name" mode which renders their name in a cursive script; both produce a signature image stored in the database
- **Transaction ID** — a UUID is generated at the moment of signing and recorded on the engagement letter; visible on the signed PDF, the post-signing confirmation page, the already-signed page, and the staff letter detail view
- **Full signing audit trail** — `signature_image` (base64 PNG), `signature_type` (drawn/typed), and `signed_user_agent` (browser/device) are now recorded alongside the existing name, date, and IP fields
- **Signature on signed PDF** — the signature image appears in the Digital Signature Record block of the PDF, above the audit rows; transaction ID shown in a highlighted box
- **Favicon on all pages** — favicon added to all standalone pages that have their own HTML head: login, forgot password, reset password, 2FA challenge, and all three engagement letter signing views

---

## v1.29 — 2026-07-08

### Added
- **Flexible job statuses** — job statuses are now fully configurable; default statuses (Pending, In Progress, Completed) are seeded automatically and can be renamed, recoloured, and reordered in the admin panel
- **Per-service job statuses** — each service can have its own set of statuses that override the global defaults; when a job is linked to a service, its status dropdown shows that service's statuses
- **Job Statuses admin panel** — new Admin → Job Statuses page; add custom statuses with a name, colour, and completion flag; drag rows to reorder within each group (global or per-service)
- **Service field on jobs** — jobs can now be linked to a service; the status dropdown updates automatically when a service is selected in the create/edit panel
- **Completion flag** — any status can be marked as a "completion" status; recurring jobs are automatically rescheduled when set to a completion status

### Changed
- Job status dropdowns throughout (jobs list, dashboard, client detail) now reflect active statuses from the database rather than hardcoded options
- Overdue/today badge logic uses the completion flag rather than checking for the literal string "completed"

---

## v1.28 — 2026-06-29

### Added
- **Re-sync with Companies House** — clients already synced with CH show a "Re-sync with Companies House" button (on both the detail page and the full edit page); pre-populates the company name in the CH search box automatically
- **Individual client type** — "Individual" added as a built-in client type; director clients created from CH officers are now automatically assigned this type
- **Already added badge** — when re-syncing, officers already stored as directors for that company are marked "Already added" in the confirmation modal; their SA checkbox is pre-ticked if SA was previously flagged
- **Remove director button** — each director in the Officers / Directors card now has a "Remove director" button to delete that record without a full re-sync

### Fixed
- **Director client type blank** — type ID is now passed directly from the panel's type map to the backend rather than relying on a name lookup that could silently fail
- **Re-sync CH button not working** — two JS bugs fixed: backslash escaping in a template literal was causing a syntax error that broke the entire panel script; Blade `{{ }}` HTML-encodes inside `<script>` blocks which produced invalid JS (`&quot;`) — switched to `{!! !!}` for JS context
- **Deleting a director client now also removes them as a director** — when an individual client linked to a company as a director is deleted, their `client_directors` record on the parent company is automatically removed, so they no longer appear as "Already added" on the next re-sync

---

## v1.27 — 2026-06-29

### Added
- **Sync with Companies House button** — appears on the client detail page for limited company / LLP / PLC clients that haven't been synced yet; opens the edit panel with the company name pre-filled in the CH search box
- **Clickable client rows** — clicking anywhere on a row in the clients list navigates to that client's detail page (links/buttons still work independently)

### Removed
- "Add Renewal" button from the client detail page header

---

## v1.26 — 2026-06-29

### Added
- **Companies House icon** — blue CH badge appears next to the company name on the client list and client detail page whenever the client has been synced with Companies House

### Changed
- **Arial font** — all PDF reports, PDF engagement letters, and the client signing portal now use Arial instead of DejaVu Sans

---

## v1.25 — 2026-06-29

### Added
- **CH Company Type** — new field (`ch_company_type`) stores the company type from Companies House (e.g. LTD, PLC, LLP); shown as a badge next to the status on the client detail page
- **Middle name** on all client edit forms and offcanvas panel

### Fixed
- **Browser address-save prompt** — renamed the client address form fields to non-standard input names (`premises`, `premises_town`, `premises_county`, `premises_postcode`) so Edge/Chrome no longer identifies them as address fields and stops offering to save them; values are remapped to the correct DB columns in the controller

---

## v1.24 — 2026-06-29

### Added
- **Middle name field** — clients now have a separate Contact Middle Name field (full edit form, offcanvas panel, and CH officer import all populate it correctly)
- When importing a CH officer as a client, names like "David John Woods" now split into first = "David", middle = "John", last = "Woods" instead of cramming middle names into the first name field

### Changed
- **Engagement letter sign page** — greeting now uses first name only ("Dear David,") instead of formal salutation ("Dear Mr Woods,")

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
