# Legacy Data Extraction

This document contains the exact names of menus, sliders, and their placements extracted from the original `db207080_eka` database. The builder should use these names when creating placeholder blocks and menus.

## Navigation Menus (by Language)

### Greek (el)
*   **Main Navigation:** `Main Greek Menu` (ID: 13)
*   **Footer:** `Footer Greek Menu` (ID: 21)

### English (en)
*   **Main Navigation:** `Main English Menu` (ID: 3315)

### Arabic (ar)
*   **Main Navigation:** `Main Arabic Menu` (ID: 3316)

*(Note: "Newspaper" Menu ID 399 also exists for Neo Fos.)*

## In-Page Sub-Navigation (Sidebars)

The legacy BeTheme used specific sidebars for top-level pages and their children. When rebuilding these pages, embed a native Navigation block in a column mapped to the corresponding menu below (respecting the current language):

*   **Establishment Pages:** (`Ίδρυση`, `Establishment`, `تأسيس`, and children like Schools)
    *   *Greek:* `Establishment Greek Menu` (ID: 70)
    *   *English:* `Establishment English Menu` (ID: 3377)
    *   *Arabic:* `Establishment Arabic Menu` (ID: 3378)
*   **Activities Pages:** (`Δράση`, `Activities`, `الأنشطة`, and children like Museums)
    *   *Greek:* `Activity Greek Menu` (ID: 71)
    *   *English:* `Activity English Menu` (ID: 3944)
    *   *Arabic:* `Activity Arabic Menu` (ID: 3945)
*   **Services Pages:** (`Υπηρεσίες`, `Services`, `الخدمات`, and children like Cemeteries)
    *   *Greek:* `Service Greek Menu` (ID: 117)
    *   *English:* `Services English Menu` (ID: 3707)
    *   *Arabic:* `Services Arabic Menu` (ID: 3716)

## Slider Placements & Media

When adding native Gutenberg Carousel/Gallery blocks, use these references for the user to import later.

### Global / Dynamic Sliders
*   **Homepage & News Page:** These do *not* use a static slider. They use a **dynamic Query Loop block** (or Carousel) that pulls the latest 5 posts from the "News" / "Ανακοινώσεις" category.

### Specific Inner Pages (Static Galleries)
The following pages require a native slider placeholder block (like `core/gallery`). The legacy slider ID is provided so the client can find the original media from the old LayerSlider/RevSlider admin panel to upload into the new block.

1.  **Staff / Στελέχωση / العاملين:** Placeholder for `staff` gallery.
2.  **Community Lounge / Κοινοτικό Εντευκτήριο / استراحة الجمعية:** Placeholder for `GREEK-CLUB` gallery.
3.  **Cemeteries / Κοιμητήρια / المقابر:** Placeholder for `Κοιμητήρια` gallery.
4.  **Cemeteries Conservation / Συντήρηση Κοιμητηρίων / المقابر:** Placeholder for `cemeteries-maintenance` gallery.
5.  **Music Museum / Μουσείο Μουσικής / متحف موسيقي:** Placeholder for `music-museum` gallery.
6.  **Science Museum / Μουσείο και Εργαστήριο Θετικών Επιστημών / متحف ومختبر:** Placeholder for `science-museum` gallery.
