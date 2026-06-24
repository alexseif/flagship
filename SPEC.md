# Spec: Legacy WordPress Modernization for ekalexandria.org (Phase 2)

## 1. Objective
* **Goal:** Complete the transition of `ekalexandria.org` to a native Gutenberg Full Site Editing (FSE) block theme (`ekalexandria-flagship`). Phase 2 focuses on recovering structural elements (menus, carousels, sub-navigation) lost during the WPBakery cleanup by utilizing the parallel original database (`db207080_eka`) as a read-only source of truth.
* **Theme Lock-down:** Utilize `theme.json` to strictly enforce design constraints (fixed color palette, typography, margins, paddings) so administrators cannot break the design or go off-script.
* **Content Extraction:** Extract specific legacy page content (like the "Αλεξανδρινός Ταχυδρόμος" newsletters) into dedicated Custom Post Types.
* **URL Preservation:** Ensure absolute 1:1 parity with legacy URLs. Permalinks must remain identical to production, requiring proper rewrite rules if structures change.
* **Database Sanitization (Deployment):** We will NOT maintain two separate databases in production. Instead, we will clean up the original production database (`db207080_eka`) by safely dropping useless tables leftover from discarded plugins, while retaining all historical news, links, and archives intact.

## 2. Tech Stack
* **WordPress Core:** WordPress 6.x (latest stable)
* **Theme Standards:** Custom Block Theme `ekalexandria-flagship` using Full Site Editing (FSE).
* **Styling Compiler:** SCSS compiled to native CSS (following WordPress official guidelines).
* **FSE Configuration:** `theme.json` for global settings and styles.
* **Parallel DB:** `db207080_eka` (used purely for querying legacy data to transform into the clean environment).

## 3. Data Recovery & Implementation Requirements
Based on the audit of missing elements post-cleanup, the following must be recovered and rebuilt using native Gutenberg blocks:

### 3.1 Global Elements
* **Top Bar:** Rebuild the top bar containing the site name and social media icons.
* **Main Navigation:** Recover the main menu, its sub-menus, and Polylang translations.

### 3.2 Carousels (Dynamic & Static)
* **Dynamic Carousels:** Rebuild the 2 dynamic carousels (Homepage and News page) pulling latest posts/news.
* **Static Carousels:** Rebuild static image carousels across the site.
* *Implementation:* We will either integrate a lightweight Gutenberg carousel block plugin or develop a custom native block, ensuring no heavy legacy slider plugins (like RevSlider/LayerSlider) are used.

### 3.3 Page-Specific Recoveries
* **Homepage:** Rebuild the 3 homepage widgets leading to *Υπηρεσίες*, *Ιστορία*, & *Ι.Ν. Ευαγγελισμού*.
* **Sub-navigation Menus:** Restore the embedded in-page/sidebar sub-menus on nested pages including:
  * `/el/ίδρυση/` and its sub-pages
  * `/el/συνέδρια-ημερίδες/`
  * `/el/κοιμητήρια/`
* **Missing Media:** 
  * Recover missing partner logos on `/el/διάφορα/σύνδεσμοι/`.
  * Recover missing icons on `/el/ανακοινώσεις-νέα/ανακοινώσεις-εκα/`.

### 3.4 Custom Post Type Extraction
* **Αλεξανδρινός Ταχυδρόμος (Neo Fos):** Extract the newsletter content and register it as a dedicated Custom Post Type (`neo_fos`).
* **Board Members:** Extract legacy board member data (previously utilizing the testimonials section) and reconstruct the single hidden Board Members page. The President's Welcome will remain a standard static page.

### 3.5 Full Site Editing (FSE) Templates
To support the clean transition and the new CPTs, the following native HTML templates must be built:
* `front-page.html`: The custom homepage layout.
* `home.html` (or `index.html`): The news/blog archive index.
* `page.html`: Default template for static pages.
* `single.html`: Default template for single news/posts.
* `archive.html`: Default template for category/tag archives.
* `single-neo_fos.html` & `archive-neo_fos.html`: Templates for the Newsletter CPT.
* `404.html`: Custom error page.
* `search.html`: Search results template.

## 4. Design Patterns & Code Style
* **Separation of Concerns:** Strict division between PHP (functionality), HTML (structure/parts), JS (behavior), and SCSS (styling). Keep them as separate as possible.
* **SCSS & theme.json Harmony:** SCSS is highly encouraged for complex, component-specific layouts (using BEM or modular patterns), while `theme.json` acts as the global design token registry (providing base CSS variables for palette, typography, and standard margins). They complement each other perfectly to keep the build lightweight.
* **No Inline Styles:** HTML block templates (`.html`) must **NOT** contain inline `style=""` attributes. All styling must be handled via CSS classes and `theme.json`. This is critical to prevent the "This block contains unexpected or invalid content" recovery error in the Block Editor.
* **Template Structure:** Prefer `.html` template parts. Only use block patterns and PHP when dynamic querying or backend functionality is required.

## 5. Boundaries & Constraints
* **Always:** Maintain 1:1 URL structure parity.
* **Always:** Keep all historical news and archives intact.
* **Never:** Drop the original database in production. We will deploy by cleaning the existing production database (dropping legacy plugin tables) rather than overwriting it with a blank one.
* **Never:** Break local builds.
* **Never:** Write inline CSS in HTML block templates.

## 6. Deployment Strategy (Digital Ocean Production)
* **Hosting Environment:** Ubuntu Server, Nginx, PHP-FPM 8.x.
* **Mechanism:** 
  1. The new `ekalexandria-flagship` theme is deployed via Git pull on the server.
  2. The production database (`db207080_eka`) is sanitized **in place**. Legacy plugins (WPBakery, LayerSlider) will be officially deleted via WP-CLI, triggering their native uninstall routines to safely remove associated tables, rather than risking manual SQL drops.
  3. Caches (Nginx FastCGI / Redis / WP Rocket) are purged.

## 7. Success Criteria
1. **Performance & SEO:** The site achieves a mobile-first responsive layout, lightweight loading, a high PageSpeed score (90+), and excellent natural SEO.
2. Site navigates seamlessly with all original menus, top bars, and in-page sub-menus working.
3. Homepage and News carousels operate dynamically using native blocks without bloated plugins.
4. Missing logos and icons are mapped back into their respective pages.
5. `Αλεξανδρινός Ταχυδρόμος` functions as a clean CPT, and the Board Members / President's Welcome pages are fully restored and visible.
6. The Block Editor loads all pages without "Block Recovery" validation errors.
7. Local deployment matches production URLs perfectly.
