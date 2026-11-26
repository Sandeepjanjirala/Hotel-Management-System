# Hospital Management System

A web application for a small hospital's daily work: registering patients, booking appointments with doctors, prescriptions, lab requests and results, pharmacy billing with stock, and a final PDF invoice that combines consultation, pharmacy and lab charges. (The repository is named "Hotel-Management-System"; the application is a hospital system.)

Built with PHP 7.4+ on CodeIgniter 3 and MySQL, with jQuery, DataTables and SweetAlert on the front end and TCPDF for invoices.

## Roles

| Role | Can do |
|---|---|
| Admin | Users, departments, doctors, patients, appointments, medicines and stock, prescriptions, pharmacy, lab, billing and reports |
| Doctor | Their appointments and patients, prescriptions, lab requests |
| Receptionist | Patient registration, appointments, consultation and pharmacy billing |
| Patient | A patient dashboard and patient records (see Known limitations) |

## How it is built

- **Controllers per role** under `application/controllers/{admin,doctor,receptionist,patient}/`. Base controllers in `application/core/MY_Controller.php` check the signed-in role before any action runs.
- **Models** (`application/models/`) use CodeIgniter's Query Builder, so values are escaped. Billing, pharmacy, lab and prescription writes run in database transactions.
- **AJAX screens**: list, add, edit, toggle and delete endpoints return JSON; `assets/js/hms-ajax.js` and `assets/js/modules/*.js` drive the tables. Notes on that layer are in [docs/AJAX_FOUNDATION.md](docs/AJAX_FOUNDATION.md).
- **Security basics**: passwords checked with `password_verify` (bcrypt), CSRF protection on, output escaped with `html_escape()` in the views.

## Run it locally (XAMPP)

1. Copy the project into `htdocs/hms` (the `.htaccess` file expects the `/hms/` path).
2. Create a MySQL database named `hms` and its tables (see Known limitations: the schema is not in this repository yet).
3. Open <http://localhost/hms/>.

With no environment variables set, the app uses the local defaults: `http://localhost/hms/`, MySQL user `root` with no password, database `hms`.

## Settings on a server

Set these as environment variables (for example `SetEnv` in the Apache virtual host). Never put real values in the repository.

| Variable | Meaning |
|---|---|
| `HMS_BASE_URL` | The site's address, ending in `/` |
| `HMS_DB_HOST`, `HMS_DB_USER`, `HMS_DB_PASSWORD`, `HMS_DB_NAME` | MySQL connection |
| `HMS_ENCRYPTION_KEY` | Only needed if CodeIgniter's Encryption class is used (it is not used today) |
| `CI_ENV` | `production` on a server, so errors are not shown to visitors |

Change the default administrator password straight after the first sign-in.

## Known limitations

- **No database schema in the repository**, so a fresh install cannot create the tables yet. Next step: export the schema (structure only, no patient data) to `database/schema.sql`, or move it into CodeIgniter migrations.
- **Patient accounts are not linked to patient records.** The patient panel's endpoints (`patient/ajax/*`) check only that the user has the patient role, then list, edit and delete *all* patient records. Do not create patient logins until each patient user is linked to their own record and every query is filtered by it.
- No automated tests.
- `system/` (CodeIgniter 3) and `application/third_party/tcpdf/` are vendored, as CodeIgniter 3 projects usually are. CodeIgniter 3 is in maintenance mode; a long-lived version would move to CodeIgniter 4.
