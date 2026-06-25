# CPT Migration Tasks

## Checkpoint: Preparation
- [ ] Verify access to `db207080_eka` credentials.
- [ ] Review `inc/custom-features.php` to ensure CPT registration conventions.

## Phase 1: Alexandrinos Tachydromos
- [ ] **Task 1.1: Registration & Configuration**
  - Register `alexandrinos_tachydromos` CPT (PHP 8.x strict types).
  - Register `group_alexandrinos_pdf` ACF field.
  - Exclude from Polylang explicitly.
  - *Acceptance:* CPT appears in admin sidebar; ACF field visible on edit screen.
- [ ] **Task 1.2: FSE Templates**
  - Create `archive-alexandrinos_tachydromos.html` with filter shortcode.
  - Create `single-alexandrinos_tachydromos.html` with PDF button shortcode.
  - *Acceptance:* Visiting `/alexandrinos_tachydromos` loads the archive block template.
- [ ] **Task 1.3: Migration CLI Command**
  - Create `wp eka migrate-tachydromos` WP-CLI command.
  - Implement read logic from `db207080_eka.wp_posts`.
  - Parse legacy layout, extract PDF/image pairs.
  - Insert idempotent records with mapped media.
  - *Acceptance:* Command runs without errors; data populates correctly on staging.

## Checkpoint: Phase 1 Review
- [ ] Verify Alexandrinos Tachydromos on frontend.
- [ ] Verify idempotency (running the command twice does not duplicate posts).

## Phase 2: Board Members
- [ ] **Task 2.1: Registration & Configuration**
  - Register `board_member` CPT.
  - Expose to Polylang via `pll_get_post_types` hook.
  - *Acceptance:* CPT appears in admin; translation icons visible in listing.
- [ ] **Task 2.2: Migration CLI Command**
  - Create `wp eka migrate-board` WP-CLI command.
  - Read legacy "Στελέχωση" grids from `db207080_eka`.
  - Extract member data and attach `_thumbnail_id`.
  - Insert idempotent records.
  - *Acceptance:* Command runs successfully; members appear in backend with languages intact.

## Checkpoint: Final Review
- [ ] Verify Board Members output in existing query loops.
- [ ] Validate staging environment readiness for production cutover.
