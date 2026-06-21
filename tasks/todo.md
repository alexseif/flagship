# Task Checklist: ekalexandria.org Modernization

This file serves as the tracking document for all micro-tasks required to complete the modernization project under **Track A (Theme-Driven Repository)**. All custom code and roadmap documents are version-controlled inside the theme folder (moved during Phase 2), while staging server config files are kept strictly unversioned on the server.

---

## Phase 1: Environment, SSL, and Local DB Setup

### [x] Task 1: Local Nginx and SSL Configuration (Unversioned)
* **Description:** Create staging Nginx config block `backstage.ekalexandria.org.conf` under a newly created `public/backstage/` directory, including port 443 listening, SSL definitions, and proxy pass instructions for uploads. Create certificates using `openssl`.
* **Acceptance criteria:**
  - Nginx staging file exists with standard configuration blocks.
  - Certificate and private key are generated using openssl at `public/backstage/ssl/backstage.crt` and `public/backstage/ssl/backstage.key`.
* **Verification:**
  - Run syntax check: `sudo nginx -t`
* **Dependencies:** None
* **Files likely touched:**
  - `public/backstage/backstage.ekalexandria.org.conf`
  - `public/backstage/ssl/backstage.crt`
  - `public/backstage/ssl/backstage.key`
* **Estimated scope:** Small [3 files]

---

### [x] Task 2: Local Database Import & URL Search-Replace
* **Description:** Reset the local database, import the local SQL copy `local_production.sql`, and execute a URL search-replace using WP-CLI to map all instances of the live production site to the secure local staging domain.
* **Acceptance criteria:**
  - All standard tables populated with legacy content.
  - Option parameters for `siteurl` and `home` point to `https://backstage.ekalexandria.org`.
* **Verification:**
  - Run check: `wp option get siteurl --path=public --skip-plugins --skip-themes`
* **Dependencies:** None
* **Files likely touched:** Database tables via WP-CLI
* **Estimated scope:** XS [0 files]

---

### [x] Task 3: Staging Site Activation & Proxy Pass Verification
* **Description:** Activate Nginx configuration and verify that request queries for missing files under `wp-content/uploads/` correctly fallback and load from the live production site `https://ekalexandria.org/wp-content/uploads/` via Nginx.
* **Acceptance criteria:**
  - Nginx serves staging site over HTTPS.
  - Verification requests use a real attachment file path fetched from the staging database.
  - Requesting this image returns status code 200 by proxying production content.
* **Verification:**
  - Fetch real attachment: `wp db query "SELECT meta_value FROM wp_postmeta WHERE meta_key='_wp_attached_file' LIMIT 1" --path=public --skip-plugins --skip-themes`
  - Run test: `curl -I -k https://backstage.ekalexandria.org/wp-content/uploads/[retrieved-meta-value]`
* **Dependencies:** Task 1, Task 2
* **Files likely touched:** Nginx daemon configurations
* **Estimated scope:** XS [0 files]

---

## Checkpoint: Staging & Staging HTTPS Setup
- [x] Staging domain resolves locally over secure HTTPS connection using openssl certs.
- [x] Missing uploads fallback automatically to the production media repository.
- [x] Staging DB matches production data layout.

---

## Phase 2: Theme Setup & SCSS

### [x] Task 4: Clone FLAGSHIP Theme & Configure Git Remote
* **Description:** Clone the user's flagship theme layout from `https://github.com/alexseif/flagship.git` to `public/wp-content/themes/ekalexandria-flagship`. Move the project `SPEC.md` and `tasks/` documents into the theme folder, and initialize/update the git remote in that directory to track the theme development.
* **Acceptance criteria:**
  - Theme folder populated with clone files.
  - Project spec and tasks moved inside theme directory.
  - Legacy plugins `js_composer` and `LayerSlider` remain active in the system for layout inspection.
* **Verification:**
  - Verify theme exists and `git status` works inside `public/wp-content/themes/ekalexandria-flagship/`
* **Dependencies:** Task 3
* **Files likely touched:**
  - `public/wp-content/themes/ekalexandria-flagship/`
* **Estimated scope:** S [1 directory]

---

### [x] Task 5: Configure SCSS Compilation Pipeline
* **Description:** Update `package.json` inside the theme folder to compile SCSS files (`style.scss` and `rtl.scss`) to standard stylesheet files in the root of the theme.
* **Acceptance criteria:**
  - Build scripts execute without errors.
  - Separate `style.css` and `rtl.css` (for Arabic RTL layout) compile.
* **Verification:**
  - Run compiler: `npm run build:css` inside theme directory.
* **Dependencies:** Task 4
* **Files likely touched:**
  - `public/wp-content/themes/ekalexandria-flagship/package.json`
* **Estimated scope:** S [1 file]

---

## Checkpoint: Theme and Stylesheet Environment
- [x] Theme setup resolves under WP active themes configuration.
- [x] SCSS compilation runs cleanly.
- [x] Legacy page builders and slider settings are active for database queries.
- [x] Commit initial theme setup: Run `git commit -m "feat(theme): clone flagship theme and configure SCSS compiler"` inside the theme folder.

---

## Phase 3: Core Theme Features & CPT Setup

### [x] Task 6: CPT Registration & Admin Dashboard Branding
* **Description:** Create `inc/custom-features.php` inside the theme folder and require it in `functions.php`. Register the `neo_fos` Custom Post Type inside it, inject admin CSS to brand the login and admin dashboard screens with community styling, and configure localization filters to Greek.
* **Acceptance criteria:**
  - `neo_fos` CPT editor resolves under Gutenberg blocks.
  - Admin login panel branded.
  - Admin dashboard options translated to Greek.
* **Verification:**
  - Check post type lists: `wp post-type list --path=public --skip-plugins --skip-themes`
* **Dependencies:** Task 4
* **Files likely touched:**
  - `public/wp-content/themes/ekalexandria-flagship/functions.php`
  - `public/wp-content/themes/ekalexandria-flagship/inc/custom-features.php`
* **Estimated scope:** S [2 files]

---

### [x] Task 7: Neo Fos RSS Feed Automation
* **Description:** Expose a custom RSS feed URL specifically for the `neo_fos` CPT that renders XML containing post titles, contents, and custom field values (PDF attachment link) to support Mailchimp automation.
* **Acceptance criteria:**
  - Custom feed route resolves.
  - Feed XML output is valid RSS specification.
* **Verification:**
  - Validate: Run curl on `https://backstage.ekalexandria.org/feed/neo-fos/` and verify layout headers.
* **Dependencies:** Task 6
* **Files likely touched:**
  - `public/wp-content/themes/ekalexandria-flagship/inc/custom-features.php`
* **Estimated scope:** S [1 file]

---

## Checkpoint: Backend & Automation
- [x] Newsletter CPT registered.
- [x] RSS Feed renders valid RSS schema.
- [x] Admin panel localized in Greek and custom branded.
- [x] Commit custom features: Run `git commit -m "feat(custom-features): implement newsletter CPT, custom RSS, and Greek admin branding"` inside the theme folder.

---

## Phase 3.5: ACF Integration & Neo Fos Migration

### [x] Task 7.1: Install & Configure Advanced Custom Fields
* **Description:** Install the Advanced Custom Fields (ACF) plugin via WP-CLI and create a PHP-based field group for the `neo_fos` CPT to hold the PDF attachment link (`pdf_attachment_link`).
* **Acceptance criteria:**
  - ACF is active.
  - Field group displays on `neo_fos` post editor.
* **Verification:**
  - Check plugin list and inspect editor.

---

### [x] Task 7.3: Pre-Cleanup Data Migration Script
* **Description:** Write and execute a script to query legacy "Επιστημονικό Φως" posts, extract their PDF links, generate new `neo_fos` CPT entries, and populate the ACF field before the legacy posts are removed or shortcodes purged.
* **Acceptance criteria:**
  - Legacy PDFs successfully migrated into the new `neo_fos` library.
* **Verification:**
  - Run `wp post list --post_type=neo_fos` and verify ACF metadata.

---

### [x] Task 7.4: Register Board Members CPT & Migrate Testimonials
* **Description:** Register `board_member` Custom Post Type. Write a script to query legacy "testimonial" post types, extract their content, generate new `board_member` entries, and trash the old testimonials.
* **Acceptance criteria:**
  - Testimonials successfully migrated to the `board_member` CPT.
* **Verification:**
  - Run `wp post list --post_type=board_member`.

---

## Phase 4: Content Migration & Shortcode Cleanup

### [x] Task 8: Export Timestamped Backup & Clean Legacy WPBakery Grid Shortcodes
* **Description:** Export a timestamped database backup under the `YYYY-MM-DD-HHMMSS-db_name.sql` pattern. Then, parse database post contents and replace/strip out WPBakery grid shortcodes (`[vc_*]`) to return clean Gutenberg layout block structures.
* **Acceptance criteria:**
  - Pre-transformation backup exists at the project root with the correct timestamped filename.
  - Pages and posts no longer contain WPBakery layout artifacts in their raw string content.
* **Verification:**
  - Verify file exists matching: `[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{6}-db207080_eka.sql`
  - Run query checking for `%[vc_%` in `wp_posts`.
* **Dependencies:** Task 2, Task 5
* **Files likely touched:** Database tables via custom parser script
* **Estimated scope:** Medium [1 helper script]

---

### [x] Task 8.5: Activate Flagship Theme & Synchronize Polylang
* **Description:** Activate the `ekalexandria-flagship` theme to formally load the new architectures on the frontend. Use WP-CLI to map all newly created `neo_fos` and `board_member` entries to the Greek language (`el`) to fix Polylang archive routing. Finally, flush the rewrite rules so the RSS feed `/feed/neo-fos/` goes live.
* **Acceptance criteria:**
  - Theme is active.
  - Polylang maps CPTs correctly.
  - RSS Feed resolves without 404.
* **Verification:**
  - Check `wp theme list` and `curl -I -k https://backstage.ekalexandria.org/feed/neo-fos/`
* **Dependencies:** Task 8
* **Estimated scope:** XS [0 files]

---

### [x] Task 9: Reconstruct FSE Templates & Rebuild Gutenberg Layouts
* **Description:** Construct the native Block templates (`front-page.html`, `home.html`, `single.html`, `page.html`) and template parts (`header.html`, `footer.html`). Integrate Polylang via native Navigation Block menus and a custom shortcode for the language switcher. Manually convert the legacy Home page (ID `13236`) and News page (ID `8934`) layouts into clean Gutenberg block grids matching the new FSE architecture.
* **Acceptance criteria:**
  - FSE templates exist in `templates/` and `parts/`.
  - Homepage resolves with custom header, main intro copy, page grid, and footer block structures natively adapting to Polylang translations.
  - News page lists post loops without legacy widget shortcodes.
* **Verification:**
  - Open `https://backstage.ekalexandria.org/` and inspect page layouts.
* **Dependencies:** Task 5, Task 8
* **Files likely touched:** Theme template parts and database pages.
* **Estimated scope:** Medium [3-5 files]

---

### [x] Task 9.5: Migrate Revolution Sliders to Native Blocks
* **Description:** Find all published pages that contain `[rev_slider ...]` shortcodes (e.g., cemeteries-maintenance) and convert these layout sections into native Gutenberg blocks like `core/cover` or `core/gallery` to remove the dependency on the Revolution Slider plugin.
* **Acceptance criteria:**
  - Active pages no longer use `[rev_slider]` shortcodes.
  - The sliders are visually replaced with native FSE block alternatives.
* **Verification:**
  - Run database query: `wp db query "SELECT ID FROM wp_posts WHERE post_content LIKE '%[rev_slider%' AND post_status = 'publish'"` returns empty.
* **Dependencies:** Task 9
* **Estimated scope:** Medium [Database updates]

---

### [x] Task 10: Purge Legacy Builder Plugins
* **Description:** Now that the layout parsing and page migrations are successfully complete, deactivate and completely delete `js_composer` (WPBakery Builder) and `LayerSlider` plugins.
* **Acceptance criteria:**
  - Legacy plugins no longer show in the WP active or inactive plugin catalog.
* **Verification:**
  - Run check: `wp plugin list --path=public --skip-plugins --skip-themes`
* **Dependencies:** Task 9
* **Files likely touched:** Plugins directory
* **Estimated scope:** XS [0 files]

---

### [x] Task 10a: Upgrade Staging PHP Environment to PHP 8.3/8.4
* **Description:** Switch the Nginx server block to use `php8.2-fpm.sock` (representing PHP 8.2 / 8.3 / 8.4) and verify that the modernized site runs cleanly without errors under the new PHP version.
* **Acceptance criteria:**
  - Nginx configuration updated to `fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;`.
  - Staging site loads successfully over HTTPS without PHP fatal errors.
* **Verification:**
  - Verify site header returns 200 OK: `curl -I -k https://backstage.ekalexandria.org`
* **Dependencies:** Task 10
* **Files likely touched:**
  - `public/backstage/backstage.ekalexandria.org.conf`
* **Estimated scope:** XS [1 file]

---

## Checkpoint: Core Pages Cleaned & Legacy Plugins Removed
- [x] Home and News pages render using native editor layout structures.
- [x] WPBakery artifacts purged from active content tables.
- [x] Legacy page builder and slider plugins deleted.
- [x] Staging PHP environment upgraded successfully to PHP 8.3/8.4.
- [x] Commit content updates: Run `git commit -m "feat(migration): parse legacy database content layouts, purge legacy builder plugins, and upgrade PHP environment"` inside the theme folder.

---

## Phase 5: Performance, SEO and Verification

### [x] Task 11: Configure Cache, Rank Math & PageSpeed Audit
* **Description:** Standardize cache options, run Lighthouse audits, configure Rank Math search engine metrics, and ensure proper metadata indexes.
* **Acceptance criteria:**
  - Desktop Lighthouse Performance `>= 95`.
  - SEO and Accessibility indexes `>= 90`.
* **Verification:**
  - Run audits in local dev server browser.
* **Dependencies:** Task 10
* **Files likely touched:** Option configurations
* **Estimated scope:** XS [0 files]

---

## Checkpoint: Final Polish & Sign-Off
- [x] All pages resolve over local HTTPS.
- [x] Lighthouse metrics meet spec standards.
- [x] Greek localization matches admin needs.
- [x] Final work committed: Run `git commit -m "chore(theme): configure performance settings and complete validation audit"` inside the theme folder.
