# Spec: Ekalexandria CPT Migration Protocol (Revised)

## 1. Objective
Complete the migration of legacy "Alexandrinos Tachydromos" (Newsletters) and "Board of Directors" data from `db207080_eka` into clean, native Custom Post Types on the flagship theme. This revised migration requires meticulous data mapping to preserve exact media assets, proper Polylang relations, precise URLs with Greek characters, and accurate publish dates derived from file uploads.

## 2. Server Prerequisites
*   **ImageMagick (PHP `imagick` extension):** Required to process and convert PDF pages into image files.
*   **Ghostscript:** Required on the server level for ImageMagick to read and rasterize PDF files.

## 3. Core Features & Acceptance Criteria

### 3.1 Board of Directors (Στελέχωση)
*   **Main Language:** The Greek version of the testimonials must be set as the canonical/primary language post.
*   **Translation Mapping:** The migration script must map and link English and Arabic versions to the Greek main post automatically based on naming conventions or legacy Polylang terms. If the relation is ambiguous or cannot be reliably determined programmatically, the script must insert the post but leave the translation unlinked for manual resolution.

### 3.2 Alexandrinos Tachydromos (Newsletters)
*   **URL Structure:** The CPT must use the exact Greek rewrite slug `αλεξανδρινός-ταχυδρόμος`. Single posts should resolve to `/αλεξανδρινός-ταχυδρόμος/{post_slug}/`. The internal CPT key will be `alx_tachydromos` to respect the WordPress 20-character limit.
*   **Gutenberg Compatibility:** The CPT must be REST API enabled (`show_in_rest => true`) to ensure full Gutenberg block editor compatibility.
*   **Archive Page:** The CPT must **NOT** be exposed to Polylang (it is not translatable). The archive will rely on the global structure without forced language prefixes, optimizing SEO.
*   **Auto-Thumbnail Generation (Editor Workflow):** When an editor adds a new "αλεξανδρινός ταχυδρόμος" and uploads a PDF via ACF, the system must automatically hook into the save process, extract the first page of the PDF using Imagick, save it as a new media attachment, and assign it as the post's Featured Image.
*   **Publish Dates (Migration Only):** The `post_date` of migrated legacy newsletters must correspond strictly to the upload date/time of its associated PDF file.
*   **Original Image Mapping (Migration Only):** Extracted thumbnail images must map to the original, un-resized upload by stripping dimension suffixes (e.g., `-724x1024.jpg`).

## 4. Tech Stack & Recommended Plugins
*   **PHP:** PHP 8.x (strict types, OOP for CLI commands).
*   **Tooling:** WP-CLI (`wp eka migrate-board`, `wp eka migrate-tachydromos`).
*   **Parsing:** `DOMDocument` for legacy HTML extraction.
*   **Localization:** Polylang functions (`pll_save_post_translations`, `pll_set_post_language`).
*   **Time-Saving Plugins:**
    *   **Advanced Custom Fields (ACF):** For PDF upload fields.
    *   **PDF Image Generator** (or similar PDF-to-thumbnail plugin): Automatically generates a featured image from the first page of a PDF upon upload. Using an established plugin for this saves us from writing and maintaining custom `Imagick` PHP hooks.

## 5. Project Structure
*   `inc/custom-features.php`: CPT registration and ACF field setup.
*   `inc/cli-commands.php`: Contains the `EKA_Migration_Command` class with both migration scripts.
*   `templates/archive-alx_tachydromos.html`: Native FSE block template for the archive (using proper query blocks).
*   `templates/single-alx_tachydromos.html`: Native FSE block template for single view with PDF button.

## 6. Design Patterns & Code Style
*   **Idempotency:** Migration scripts must be repeatable without duplicating data.
*   **Error Handling:** WP-CLI commands should output clear success, warning, and skipping states.
*   **Direct DB Queries:** Due to data residing in `db207080_eka`, use `$wpdb` direct queries securely (using `$wpdb->prepare` where variables are involved) to fetch legacy posts and meta.

## 7. Boundaries & Constraints
*   **NEVER** sideload or duplicate existing media library files during migration.
*   **ALWAYS** maintain the exact Greek rewrite slug for the newsletter CPT.
*   **ALWAYS** verify `imagick` is loaded and supports PDF before attempting extraction.
