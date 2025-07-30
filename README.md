# pnb-cls

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
