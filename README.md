# Event Venue Management System (EVMS)

A full working website built from your synopsis: HTML/CSS/JavaScript front-end + PHP/MySQL back-end, with the exact tables from your Data Dictionary and all 11 modules represented.

## How to run it (XAMPP, as in your Feasibility Study)

1. Install **XAMPP** (or WAMP) and start **Apache** + **MySQL** from the control panel.
2. Copy the whole `evms` folder into your XAMPP `htdocs` directory
   (e.g. `C:\xampp\htdocs\evms`).
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`), create nothing manually —
   just click **Import**, choose `database/schema.sql`, and run it.
   This creates the `evms` database, all 8 tables, and some sample venues/owners.
4. If your MySQL has a password on the `root` user, update `config.php` with it.
5. Visit `http://localhost/evms/` in your browser.

## Logins

- **Customer:** register a new account from "Sign up," or use the demo flow: browse → book → pay.
- **Admin:** go to `http://localhost/evms/admin/login.php`
  - Email: `admin@evms.com`
  - Password: `admin123`

## What's included (mapped to your synopsis)

| Synopsis module | Where it lives |
|---|---|
| Customer Management | `register.php`, `login.php`, session-based auth |
| Venue Information Management | `index.php` (search/filter), `venue_details.php` |
| Reservation & Booking | `book.php`, double-booking prevention on venue+date |
| Payment Processing | `payment.php`, `payments` table, receipt on confirm |
| Venue Owner Management | `admin/owners.php` |
| Event Coordination | `events` table (event type, guest count, notes) |
| Review and Rating | `feedback.php` (only for confirmed bookings) |
| Report Management | `admin/reports.php` (revenue, cancellations, ratings, owner summary) |
| Venue Schedule & Availability | `availability` field + conflict check in `book.php` |
| Administrator & Authentication | `admin/` folder, separate `admin` table, `password_hash()` |
| Validation checks from synopsis | `js/script.js` (required fields, numeric, future-date-only) + server-side checks in PHP |

## What's new in this update

- **Redesigned homepage** with an image hero + search bar, a "Popular Event Categories" grid
  (Weddings, Birthday Parties, Corporate Events, Social Gatherings, Engagements — each one links
  to real filtered results), a "How It Works" section, a "What Our Users Say" section (pulls real
  4–5 star reviews once you have some, and shows sample quotes until then), and a closing call‑to‑action band.
- **New public pages:** `about.php` (story + live stats pulled from your database), `contact.php`
  (saves messages to a new `contact_messages` table) and `list_venue.php` (a lead form for venue
  owners, saved to a new `venue_leads` table).
- **New admin page:** `admin/leads.php` — view and manage venue-owner leads and contact messages,
  with a "new leads" banner on the dashboard.
- **Navigation/footer** rebuilt with a proper multi-column footer, social icons, a mobile menu
  toggle, and "List Your Venue" / "Sign In" actions in the header.
- The two new tables (`contact_messages`, `venue_leads`) are created automatically the first time
  the site runs (see `config.php`), so this update works even on a database you already imported —
  no need to re-run `schema.sql` (though it's included there too for fresh installs).

## Notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never stored in plain text.
- All SQL queries use escaping (`mysqli_real_escape_string`) to reduce injection risk; for a production system you'd move to prepared statements throughout.
- The payment step is simulated (no real gateway) since the synopsis lists advanced payment gateway integration under **Future Scope**.
- Design: a warm ivory/ink/brass palette with a serif display face, meant to feel like an event invitation rather than a generic admin template.
