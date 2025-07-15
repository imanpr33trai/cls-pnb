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