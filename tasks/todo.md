# CPT Migration Tasks

## Checkpoint: Preparation (Phase 1)
- [x] Verify access to `db207080_eka` credentials.
- [x] Review `inc/custom-features.php` to ensure CPT registration conventions.
- [x] Verify Server Prerequisites: Check if `imagick` PHP extension and Ghostscript are active on the staging server.
- [x] Install and activate required plugins: **Advanced Custom Fields (ACF)** and a **PDF Image Generator** plugin.
- [x] **Commit:** Atomic commit for any prerequisite configuration updates.

## Phase 2: Alexandrinos Tachydromos (Newsletters)
- [x] **Task 2.1: Registration & Configuration**
  - Register `alx_tachydromos` CPT (PHP 8.x strict types, `show_in_rest` => true for Gutenberg) with rewrite slug `αλεξανδρινός-ταχυδρόμος`.
  - Register ACF fields for the PDF file.
  - Exclude `alx_tachydromos` from Polylang explicitly so it is not translatable.
  - *Acceptance:* CPT appears in admin sidebar; Gutenberg editor works; ACF field visible on edit screen; URL resolves correctly.
  - [x] **Verify**
  - [x] **Commit**
- [x] **Task 2.2: FSE Templates**
  - Create `templates/archive-alx_tachydromos.html` using proper query blocks.
  - Create `templates/single-alx_tachydromos.html` with a PDF button.
  - *Acceptance:* Visiting `/αλεξανδρινός-ταχυδρόμος` loads the archive block template.
  - [x] **Verify**
  - [x] **Commit**
- [x] **Task 2.3: Migration CLI Command**
  - Create `wp eka migrate-tachydromos` WP-CLI command in `inc/cli-commands.php`.
  - Implement read logic from `db207080_eka.wp_posts` using `$wpdb`.
  - Parse legacy layout by iterating `.pdf` links, finding corresponding `<img>` and titles.
  - Map `post_date` strictly to PDF file upload date.
  - Strip dimension suffixes from image names to locate original `_thumbnail_id`.
  - Insert idempotent records.
  - *Acceptance:* Command runs idempotently without errors; data populates correctly without duplicating media.
  - [x] **Verify**
  - [x] **Commit**

## Checkpoint: Phase 2 Review
- [ ] Verify Alexandrinos Tachydromos on frontend (Greek URLs, no language prefix).
- [ ] Verify publish dates match PDF uploads.
- [ ] Verify idempotency (running the command twice does not duplicate posts).
- [ ] Verify the PDF Image Generator plugin auto-generates thumbnails upon new PDF uploads in the editor.

## Phase 3: Board Members
- [ ] **Task 3.1: Registration & Configuration**
  - Register `board_member` CPT.
  - Expose to Polylang via `pll_get_post_types` hook.
  - *Acceptance:* CPT appears in admin; translation icons visible in listing.
  - [ ] **Verify**
  - [ ] **Commit**
- [ ] **Task 3.2: Migration CLI Command**
  - Create `wp eka migrate-board` WP-CLI command in `inc/cli-commands.php`.
  - Read legacy "Στελέχωση" grids from `db207080_eka`.
  - Extract member data and attach `_thumbnail_id`.
  - Map Greek as the primary/canonical language.
  - Link English and Arabic versions automatically based on naming conventions/Polylang terms.
  - Leave ambiguous relations unlinked for manual resolution.
  - Insert idempotent records.
  - *Acceptance:* Command runs successfully; members appear in backend with correct Greek canonical and linked translations.
  - [ ] **Verify**
  - [ ] **Commit**

## Checkpoint: Final Review
- [ ] Verify Board Members output in existing query loops.
- [ ] Validate staging environment readiness for production cutover.
