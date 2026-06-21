# Implementation Plan: Legacy WordPress Modernization for ekalexandria.org

## Overview
This plan outlines the systematic modernization of `ekalexandria.org` on a local staging environment `https://backstage.ekalexandria.org` running initially under **PHP 7.4** (transitioning to **PHP 8.3/8.4 (LTS)** after legacy page builder plugin deactivation) and **WordPress 6.x**. The project replaces the legacy WPBakery Builder (`js_composer`) and `LayerSlider` with a custom block theme named `ekalexandria-flagship` (cloned from [flagship](https://github.com/alexseif/flagship)). The custom layouts, custom post type `neo_fos`, custom RSS feeds, and admin panel overrides are coded directly inside the theme directory (`ekalexandria-flagship`) to maintain a single self-contained repository under git control. Media uploads are dynamically proxied via Nginx to bypass the 50 GB local download, and local SSL is generated using `openssl`.

---

## Architecture Decisions
1. **Theme-Driven Customizations:** To ensure simple deployment and clean tracking, CPT registrations, custom RSS templates, and admin styles are written in `inc/custom-features.php` inside the theme folder, eliminating the need for a separate custom plugin.
2. **Separation of Styling (SCSS) and Block Structure:** No styles will be hardcoded inside HTML block templates or inline properties. Design styles will be handled entirely via compiled SCSS files (`style.css` and `rtl.css` for multilingual support) to ensure block editor integrity.
3. **Reverse Proxying for Media Uploads:** To bypass downloading the 50 GB uploads folder from production, the local Nginx configuration will intercept requests to `wp-content/uploads/` and dynamically proxy missing assets to the live site.
4. **No-API Mailchimp Integration:** Mailchimp will query a dedicated RSS feed exposed for the `neo_fos` CPT (e.g. `https://backstage.ekalexandria.org/feed/neo-fos/`). This avoids maintaining fragile Mailchimp API tokens in the legacy database.
5. **Local SSL Setup:** Running local staging strictly over HTTPS (via `openssl` self-signed certs) prevents mixed content issues and ensures proper loading of Gutenberg blocks and REST resources.
6. **Project-Scoped Agent Guidelines:** A guiding principle rule file [AGENTS.md](file:///var/www/ekalexandria.org/.agents/AGENTS.md) is created to keep all future AI subagents aligned on these architectural decisions.

---

## Data Migration & Integration Strategy

### 1. Advanced Custom Fields (ACF) Integration
To properly structure PDF attachments for the **Neo Fos** (Αλεξανδρινός Ταχυδρόμος) archive, we will install ACF and define a PHP-based field group containing `pdf_attachment_link`. This replaces unstructured links with filterable metadata.

### 2. Pre-Cleanup Custom Migrations
Before performing destructive database cleanups on legacy `wp_posts` (like removing WPBakery `[vc_*]` tags), we execute targeted extraction scripts:
- **Neo Fos Extraction:** Parse the single page "αλεξανδρινός-ταχυδρόμος" to extract all `<a href="...pdf">` anchors, creating individual `neo_fos` Custom Post Types for each issue.
- **Board Members:** Extract old `testimonial` posts and migrate them into the new `board_member` Custom Post Type.

### 3. Polylang Compatibility (Language Assignments)
Since Polylang is strictly managing language routes (`/el/` and `/ar/`), any programmatically created Custom Post Type (like `neo_fos` or `board_member`) MUST be assigned a default language (Greek) during migration. Otherwise, they will be orphaned in the database and cause 404s on archive and RSS feed routes.

### 4. FSE Template Hierarchy Reconstruction
The cloned `flagship` theme is a minimal baseline. To fully support the complex layout required by Polylang (static homepages, distinct blog archives, custom post type singles), we must reconstruct a standard WordPress Full Site Editing template hierarchy:
- `front-page.html`: The static homepage template leveraging Gutenberg Post Content to dynamically output the Greek or Arabic page contents.
- `home.html`: The blog index leveraging the Query Loop block inherited from the global query.
- `single.html` & `page.html`: Universal single-view wrappers.
- `parts/header.html`: Universal header containing a dynamic Navigation Menu block (swapped automatically by Polylang) and a custom Language Switcher.

---

## Risks and Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| Nginx reverse proxy CORS block | Medium | Configure headers inside the proxy block to allow cross-origin requests from `https://ekalexandria.org` to staging. |
| Incomplete WPBakery conversions | High | Write a PHP database parser that replaces standard `[vc_*]` grid tags with plain layout blocks, and test it incrementally on single draft pages before updating the live database table. |
| PHP 8.3 Fatal Errors in old plugins | High | Run PHPStan or quick WP-CLI syntax audits. Disable offending secondary legacy plugins and update core plugins. |
| RTL styles breaking layouts | Medium | Separate mobile-first base SCSS from layout adjustments, using dedicated `rtl.scss` structures loaded only when Polylang detects RTL locales (Arabic). |

---

## Open Questions
* **RTL styles source:** Do we have existing style elements from Betheme we must map to `ekalexandria-flagship`'s RTL layouts, or do we construct them cleanly from scratch?
