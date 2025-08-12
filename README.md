# pnb-cls

for clone 
git clone -b url_refine https://github.com/imanpr33trai/cls-pnb.git
cd cls-pnb
composer i

## URL Structure Changes

This project has been updated to use SEO-friendly and readable URLs (slugs) instead of ID-based URLs.

**Old URL Examples:**
- `product.php?id=123`
- `single-ad.php?ad_id=456`
- `single-article.php?id=789`

**New URL Examples:**
- `/products/` (for ad listings, handled by `product.php`)
- `/ads/ad-title-slug/`
- `/articles/article-title-slug/`
- `/categories/category-name-slug/`

### Database Schema Updates

To support the new URL structure, the following `slug` columns have been added to the respective tables:

- `ad_form` table: Added `ad_slug` (VARCHAR(255) UNIQUE)
- `blog_posts` table: Added `blog_slug` (VARCHAR(255) UNIQUE)

**Action Required:** If you are setting up this project, ensure your database schema is updated to include these new columns. You can find the `ALTER TABLE` statements in `schema-update.sql`.

## Production Readiness Checklist

This outline details recommended changes to prepare the application for a production environment.

### 1. Security Enhancements (High Priority)

*   **SQL Injection Vulnerabilities:**
    *   **Files:** `partials/search-hero.php`, `partials/voice-search.php`, `partials/get_subcategories.php`.
    *   **Issue:** These files directly insert user input into database queries without using prepared statements, creating a critical security risk.
    *   **Action:** Rewrite all database queries in these files to use prepared statements (`prepare`, `bind_param`, `execute`).
*   **Cross-Site Scripting (XSS) Prevention:**
    *   **Issue:** User-provided data (`$_POST`, `$_GET`) is not consistently sanitized before being displayed on the page.
    *   **Action:** Review all `echo` statements and ensure `htmlspecialchars()` is used for any user-controllable data to prevent XSS attacks.

### 2. Code Cleanup & Error Handling

*   **Remove Debugging Code:**
    *   **Files:** `app/auth/login.php`, `app/auth/logout.php`, `app/auth/register.php`, `app/auth/verify.php`.
    *   **Issue:** These files contain active or commented-out debugging code (`echo`, `print_r`, etc.) that should not be in production.
    *   **Action:** Remove all debugging statements.
*   **Disable Public Error Display:**
    *   **File:** `app/auth/login.php`.
    *   **Issue:** `ini_set('display_errors', 1)` is active, which can reveal sensitive server information.
    *   **Action:** This setting should be disabled in production. Global error reporting settings should be managed centrally in `config/config.php`.
*   **Consistent Error Handling:**
    *   **Issue:** Error handling is inconsistent across the application (e.g., `die()`, session messages, direct `echo`).
    *   **Action:** Implement a unified error handling strategy. Log system-level errors to a file and show user-friendly messages for UI errors.

### 3. Code Organization & Best Practices

*   **Redundant File Inclusions:**
    *   **Issue:** Some files use `include` instead of `include_once`, which can lead to "cannot redeclare function" errors if a file is included multiple times.
    *   **Action:** Use `include_once` or `require_once` for all file inclusions to ensure they are loaded only once.
*   **Separation of Concerns:**
    *   **Issue:** PHP logic is heavily mixed with HTML markup, making the code difficult to maintain.
    *   **Action (Short-term):** Restructure files to have a dedicated PHP block at the top for all logic (database queries, form processing), storing results in variables. The HTML below should then use these variables for display.
*   **Hardcoded URLs:**
    *   **Issue:** Some files contain hardcoded URLs instead of using the global base URL variable.
    *   **Action:** Update all URLs to be dynamically generated using the `$base_url` variable defined in `config/config.php`.

## Application Architecture & Production Outline

This section provides a high-level overview of the application's structure and a checklist for deploying to a production environment.

### 1. Core Configuration (`/config/`)

This directory is the application's foundation.

*   **`config.php`**:
    *   **Purpose**: Initializes the entire application. Loads environment variables (`.env`), establishes the database connection, and configures the session handler.
    *   **Production Action**: Ensure the `.env` file exists on the server with correct production database credentials, API keys, and SMTP settings. The `.env` file must **never** be committed to version control.

*   **`debug.php`**:
    *   **Purpose**: Provides a detailed debugging and error-handling system for development.
    *   **Production Action**: Set the `IS_DEVELOPMENT_MODE` constant to `false`. This will disable all on-screen error reporting and debug outputs, preventing potential information leaks. Error logging to `logs/debug.log` will continue.

*   **`functions.php`**:
    *   **Purpose**: Contains global helper functions like `create_unique_slug()`.
    *   **Production Action**: No changes needed, but ensure any new helpers are generic and well-documented.

### 2. Authentication Flow (`/app/auth/`)

This directory manages the entire user lifecycle.

*   **Files**: `login.php`, `register.php`, `verify.php`, `logout.php`.
*   **Purpose**: Handles user sign-in, new account creation, OTP email verification, and sign-out. It integrates with Google and GitHub for social logins.
*   **Production Action**:
    *   **Remove Debug Code**: Purge all commented-out or active `echo`, `print_r`, and `var_dump` statements.
    *   **User-Friendly Errors**: Ensure that error messages shown to the user (e.g., "Invalid email or password") are generic and do not reveal whether an email address exists in the system.

### 3. Main Pages (`/app/pages/`)

These are the primary user-facing views of the application.

*   **Files**: `home.php`, `ad-form.php`, `Blog-form.php`, `single-ad.php`, `single-article.php`, etc.
*   **Purpose**: These files are responsible for fetching data from the database and rendering the main content of the application. They follow a consistent pattern:
    1.  Include `config.php`.
    2.  Perform all necessary PHP logic (database queries, form processing).
    3.  Include `header.php`.
    4.  Render the HTML body, using the data fetched in the logic block.
    5.  Include `footer.php`.
*   **Production Action**:
    *   **Secure All Queries**: Confirm that every database query uses prepared statements (`prepare`, `bind_param`, `execute`) to prevent SQL injection.
    *   **Escape All Output**: Ensure all data echoed into the HTML from the database or user input is sanitized with `htmlspecialchars()` to prevent XSS attacks.

### 4. Reusable View Components (`/partials/`)

This directory contains snippets of UI used across multiple pages.

*   **Files**: `header.php`, `footer.php`, `hero-sec.php`, `category-sec.php`, etc.
*   **Purpose**: To provide consistent and reusable parts of the user interface.
*   **Production Action**:
    *   **CRITICAL - Remove Insecure Scripts**: The files `partials/search-hero.php` and `partials/voice-search.php` contain severe SQL injection vulnerabilities. They should be **deleted immediately**, as their functionality has been replaced by secure AJAX handlers.
    *   **CRITICAL - Consolidate JavaScript**: The `footer.php` contains multiple, conflicting, and redundant JavaScript blocks. This will lead to unpredictable behavior. All page-specific JavaScript should be moved into a single, well-organized file (e.g., `assets/js/main.js`) and included once. The current inline scripts should be removed.
    *   **Cleanup**: Delete legacy handlers like `partials/submit_review.php` that have been replaced by AJAX endpoints.

### 5. AJAX Endpoints (`/ajax/`)

This directory is the designated location for all server-side logic that responds to client-side JavaScript requests.

*   **Purpose**: To provide clean, JSON-based responses for dynamic page updates (e.g., submitting comments, fetching search results, loading posts by category).
*   **Production Action**:
    *   **Enforce AJAX Mode**: Every file in this directory **must** begin with `define('AJAX_REQUEST', true);` to disable the HTML-based error reporting from `debug.php`.
    *   **Standardize Responses**: All endpoints should return JSON objects (e.g., `{"success": true, "data": [...]}` or `{"success": false, "message": "Error"}`). They should never `echo` raw HTML or strings.

### 6. Deployment Workflow

1.  **Dependencies**:
    *   Run `composer install --no-dev --optimize-autoloader` on the server to install only production PHP dependencies.
    *   Run `npm install` and `npm run build` (or equivalent) to compile and minify production CSS and JavaScript assets.
2.  **Environment File**: Create the `.env` file on the production server with the correct database, mail, and API credentials.
3.  **Configuration**: In `config/debug.php`, set `IS_DEVELOPMENT_MODE` to `false`.
4.  **Permissions**: Ensure the `logs/` and `assets/uploads/` directories are writable by the web server.
5.  **Cron Job**: Set up a cron job on the server to periodically run the `partials/update_ad_status.php` script to handle ad expirations.
