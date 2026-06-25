# Custom Post Type Migration Spec

## 1. Alexandrinos Tachydromos (Αλεξανδρινός Ταχυδρόμος)
**Goal:** Migrate the legacy newsletter PDFs and images into a modern, block-native Custom Post Type (CPT).

### Registration Details
- **CPT Slug:** `alexandrinos_tachydromos`
- **Labels:** 
  - Name: Αλεξανδρινός Ταχυδρόμος
  - Singular: Τεύχος
- **Supports:** `title`, `editor`, `thumbnail`, `excerpt` (NO native `custom-fields` to prevent UX confusion).
- **Settings:** `has_archive` = true, `show_in_rest` = true (Block Editor enabled).

### Custom Fields (ACF)
- **Field Group:** `group_alexandrinos_pdf`
- **Field Name:** `alexandrinos_pdf_file`
- **Type:** `file`
- **Return Format:** `id` (Crucial for extracting thumbnails).
- **Position:** `side` (Ensures it appears in the Gutenberg Document Sidebar).
- **Conditional Logic:** Show only if post_type == `alexandrinos_tachydromos`.

### Polylang Integration
- Expose `alexandrinos_tachydromos` to Polylang via `pll_get_post_types` filter so each issue can be tied to its Arabic/English translation natively.

### FSE Templates
- `archive-alexandrinos_tachydromos.html`: Will use a `core/query` block. Must include a custom shortcode `[alexandrinos_archive_filter]` for Month/Year `<select>` dropdown filtering.
- `single-alexandrinos_tachydromos.html`: Must include a custom shortcode `[alexandrinos_pdf_link]` to generate a styled "Download / View PDF" block button.

### Migration Logic
1. Pull legacy layout from page ID `7380` (Αλεξανδρινός Ταχυδρόμος).
2. Use regex to extract pairs: `<a href="...pdf">` and `<img class="wp-image-{ID}">`.
3. Create new `alexandrinos_tachydromos` posts.
4. Set the `_thumbnail_id` directly from the extracted image IDs.
5. Populate the `alexandrinos_pdf_file` ACF field with the corresponding PDF URL or ID.
6. Normalize the post titles into "Greek Month Year" format.

---

## 2. Board Members (Μέλη ΔΣ)
**Goal:** Migrate the legacy Board of Directors grid into a translatable CPT.

### Registration Details
- **CPT Slug:** `board_member`
- **Labels:** 
  - Name: Μέλη ΔΣ
  - Singular: Μέλος ΔΣ
- **Supports:** `title`, `editor`, `thumbnail`, `excerpt`.
- **Settings:** `has_archive` = false (Usually displayed via query loops on pages), `show_in_rest` = true.

### Polylang Integration
- Must be explicitly exposed to Polylang via `pll_get_post_types` filter to ensure members can be associated across EN/AR/EL.

### Migration Logic
1. Extract board members from legacy "Στελέχωση" shortcodes/grids.
2. Ensure any legacy WPBakery attached images are properly set as the `_thumbnail_id`.
3. Assign them to `board_member` CPT.
