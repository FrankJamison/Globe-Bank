# Globe Bank (PHP / MySQL)

A small CMS-style PHP site with a public-facing navigation (Subjects → Pages) and a password-protected staff area for managing content and admins.

## Tech stack

- PHP (uses `mysqli`)
- MySQL / MariaDB
- Server-side sessions for staff authentication

## Project layout

- `public/` – Web root (public site + staff area)
- `private/` – App code, DB connection, shared templates (should not be web-accessible)
- `setup_database.sql` – Local/dev database schema + seed data
- `index.php` – Convenience redirect into `public/`

## How routing/URLs work

The app computes `WWW_ROOT` dynamically in `private/initialize.php` by finding the `/public` segment in the current request path. This allows the site to run either:

- with your web server’s document root pointed at `public/` (recommended), or
- with the project root served and relying on `index.php` to redirect into `public/`.

## Local setup

### 1) Requirements

- PHP 8.x recommended (7.4+ likely works)
- MySQL 5.7+ or MariaDB 10.x
- A web server (Apache/Nginx), or PHP’s built-in server for quick testing

### 2) Configure the web root (recommended)

Configure your web server so the site’s document root points to the `public/` directory.

This prevents direct web access to `private/`, which contains database credentials and internal code.

### 3) Create and seed the database

1. Create the schema and seed data by running:

   - `setup_database.sql` (creates database `globe_bank` and inserts sample Subjects/Pages)

2. The script also includes optional statements to create a MySQL user and grant privileges.

If you prefer to run commands manually, the minimum you need is:

- A database named `globe_bank`
- Tables: `subjects`, `pages`, `admins`
- Seed rows for `subjects` and `pages` (optional)

### 4) Configure database credentials

Edit:

- `private/db_credentials.php`

Defaults are:

- `DB_SERVER`: `localhost`
- `DB_USER`: `globe_user`
- `DB_PASS`: `globe_password`
- `DB_NAME`: `globe_bank`

The database connection is created in `private/database.php` and initialized via `private/initialize.php`.

## Running the app

### Option A: Web server (Apache/Nginx)

- Point the site’s document root to `public/`
- Visit the site using whatever URL your server is configured to serve (for example `http://localhost/` or a local domain you set up)

### Option B: PHP built-in server (quick dev)

From the project root:

```bash
php -S localhost:8000 -t public
```

Then browse to:

- `http://localhost:8000/`

## Staff area

- Staff login: `/staff/login.php`
- Staff menu: `/staff/index.php`

### Creating the first admin (important)

The Admin management pages require login, so on a fresh database there is no “first admin” account you can create through the UI.

To bootstrap the first admin, insert a row into the `admins` table with a bcrypt hash.

1. Generate a bcrypt hash with PHP:

```bash
php -r "echo password_hash('ChangeMe123!@#', PASSWORD_BCRYPT), PHP_EOL;"
```

2. Insert the admin (replace values as needed):

```sql
INSERT INTO admins (first_name, last_name, email, username, hashed_password)
VALUES ('First', 'Admin', 'admin@example.com', 'admin_user', '$2y$10$...');
```

Password rules enforced by the UI/validators:

- 12+ characters
- at least one uppercase letter
- at least one lowercase letter
- at least one number
- at least one special character

After creating the first admin, you can log in and create additional admins via:

- Staff → Admins → “Create New Admin” (`/staff/admins/new.php`)

## Content editing

- Subjects and Pages are managed in the staff area.
- Public navigation shows only records marked visible.
- The public page renderer allows a limited set of HTML tags (see `public/index.php`).

## Troubleshooting

- **Blank page / redirect loops**: Confirm your document root points to `public/`, or that you’re entering through the project `index.php` which redirects to `public/index.php`.
- **Database connection failed**: Verify `private/db_credentials.php` matches your MySQL host/user/password/database.
- **Port issues (built-in server)**: Change `8000` to any free port.

## Notes for deployment

- Use `setup_database_production.sql` only if you want to target a different database name.
- Never deploy `private/db_credentials.php` with development passwords.
- Ensure `private/` is not web accessible in production.
