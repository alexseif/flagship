# Spec: Legacy WordPress Modernization for ekalexandria.org

## 1. Objective
* **Goal:** Modernize the legacy `ekalexandria.org` WordPress site (initially built 11 years ago, currently running WordPress core with local customizations) to run initially under **PHP 7.4** (to maintain legacy builder compatibility during content translation) and transition to a modern **PHP 8.3/8.4 (LTS)** environment once the layout is converted and legacy page builders are purged.
* **Theme Rebuild (Single Repository):** Rebuild the frontend using a lightweight, native Gutenberg block theme called `ekalexandria-flagship` based on the style and standards of [flagship](https://github.com/alexseif/flagship). Avoid inline styling inside the HTML templates so as not to break the block editor, using clear HTML sections and PHP patterns/custom blocks where needed.
* **Theme-Driven Customizations:** To maintain a clean and self-contained codebase, all custom functionalities (the `neo_fos` CPT registry, the custom RSS newsletter feed, and the Greek admin branding styles) will be written directly inside the theme under `inc/custom-features.php` (required by `functions.php`).
* **Styling & Layout:** Use **SCSS** for styling, compiled into native stylesheets, following **mobile-first responsive design** practices.
* **Multilingual & RTL:** Retain multilingual capabilities (Greek, English, and Arabic) and ensure full RTL (Right-to-Left) stylesheet support for the Arabic layout.
* **Media Asset Optimization:** Proxy requests for missing local uploads dynamically to the live production site (`https://ekalexandria.org/wp-content/uploads/`) using Nginx configurations, avoiding the need to download the 50 GB uploads folder.
* **Content Refactoring & Automation:** Convert the "Neo Fos" newsletter from a manual monthly PDF page upload into a Custom Post Type (CPT) inside the theme that automates Mailchimp updates (via custom RSS feed or draft campaign triggers).
* **Local SSL & Staging Configuration:** Configure the local staging site `backstage.ekalexandria.org` with local SSL certificates generated via `openssl` to ensure secure connections and uniform production-ready URLs.
* **Gated Plugin Deactivation:** Retain legacy plugins active during the setup and layout mapping phases. Only deactivate and delete them after the layout conversion is successfully mapped and blocks are generated.
* **Version Control Strategy (Track A):** Maintain a lightweight Git repository **only** inside the custom theme folder `public/wp-content/themes/ekalexandria-flagship/` (initialized during implementation). This repository tracks theme stylesheets, layouts, custom functions, specification documentation (`SPEC.md`), and task checklists. All WordPress core files, third-party vendor plugins, database dumps, and private SSL keys are strictly excluded.
* **Admin UX:** Brand the WP Admin dashboard and localize it to Greek for non-technical community administrators.

---

## 2. Tech Stack
* **WordPress Core:** WordPress 6.x (latest stable)
* **PHP Version:** PHP 7.4 (initial migration/cleanup phase) -> PHP 8.3 / 8.4 (LTS post-migration)
* **Web Server:** Nginx (no Apache)
* **Database:** MySQL 8.0+
* **Version Control:** Git (tracking custom theme path only, setup during implementation)
* **Local SSL:** Certificates generated via `openssl`
* **Theme Standards:** Custom Block Theme `ekalexandria-flagship` (derived from `https://github.com/alexseif/flagship`)
* **Styling Compiler:** Dart Sass (compiling `.scss` to `style.css` and `rtl.css`)
* **Key Plugins:**
  * **Retain & Update:** `polylang` (multilingual translation), `seo-by-rank-math` (SEO), `mailchimp` (newsletter coordination), `contact-form-7` (forms).
  * **Retain during migration / Deactivate post-migration:** `js_composer` (WPBakery Builder), `LayerSlider`.
  * **Delete:** `display-posts-shortcode`, `google-captcha`.

---

## 3. Commands
Full executable commands for setting up and managing the staging environment:

### Local SSL Generation (using openssl)
```bash
# Generate self-signed SSL key and cert for backstage.ekalexandria.org
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout backstage.key -out backstage.crt -subj "/CN=backstage.ekalexandria.org"
```

### Local DB Import & Clean Setup
```bash
# Recreate local database
wp db reset --yes --path=public --skip-plugins --skip-themes

# Import the existing local copy of the production database
wp db import local_production.sql --path=public --skip-plugins --skip-themes

# Export database backups using YYYY-MM-DD-HHMMSS-db_name format
wp db export "$(date +%Y-%m-%d-%H%M%S)-db207080_eka.sql" --path=public --skip-plugins --skip-themes

# Search and replace production URLs for the local Nginx domain (using secure HTTPS scheme)
wp search-replace "https://ekalexandria.org" "https://backstage.ekalexandria.org" --path=public --skip-plugins --skip-themes
```

### Theme Repository Git setup (cloning flagship)
```bash
# Create staging configuration folder inside public
mkdir -p public/backstage/

# Clone flagship theme repository
git clone https://github.com/alexseif/flagship.git public/wp-content/themes/ekalexandria-flagship

# Go to the theme directory
cd public/wp-content/themes/ekalexandria-flagship

# Move spec and tasks from project root into theme folder
mv ../../../SPEC.md .
mv ../../../tasks/ .

# Point git remote to your staging repository or update remote url
git remote set-url origin [your-repository-url]
```

### SCSS Compilation (Theme Directory)
```bash
# Compile theme stylesheets from SCSS sources
npm run build:css
```

### Plugin Operations (Executed post-migration)
```bash
# Deactivate and delete bloated/legacy plugins ONLY AFTER content parsing is complete
wp plugin deactivate js_composer LayerSlider --path=public --skip-plugins --skip-themes
wp plugin delete js_composer LayerSlider --path=public --skip-plugins --skip-themes
```

### Web Server Control (Nginx)
```bash
# Verify Nginx configuration syntax
sudo nginx -t

# Reload Nginx server blocks
sudo systemctl reload nginx
```

---

## 4. Project Structure (Staging Context)
Once the theme is cloned and documents are moved inside the repository in the implementation phase, files will reside in the following locations:

```
public/
  ├── backstage/                        → Staging configurations (Nginx & SSL keys)
  │   ├── ssl/                          → Staging SSL certificate storage (generated by openssl)
  │   │   ├── backstage.key
  │   │   └── backstage.crt
  │   └── backstage.ekalexandria.org.conf → Staging Nginx configuration template (RTL & upload proxy)
  ├── wp-content/
  │   ├── themes/
  │   │   └── ekalexandria-flagship/    → Theme folder under Git control
  │   │       ├── SPEC.md               → Specification document
  │   │       ├── tasks/                → Development roadmaps & task lists
  │   │       │   ├── plan.md
  │   │       │   └── todo.md
  │   │       ├── package.json          → SCSS build scripts
  │   │       ├── theme.json            → Color palette, layout, typography (timing pending design approval)
  │   │       ├── functions.php         → Core theme setups & RTL stylesheet queues
  │   │       ├── style.css             → Compiled styling
  │   │       ├── rtl.css               → Compiled RTL stylesheet
  │   │       ├── inc/
  │   │       │   └── custom-features.php → CPT registry, custom feed, and admin branding styling
  │   │       ├── sass/                 → SCSS source files
  │   │       │   ├── style.scss
  │   │       │   └── rtl.scss
  │   │       ├── templates/            → Home, Archive, Single, Page templates
  │   │       └── parts/                → Header, Footer, and template components
```

---

## 5. Code Style
* **PHP:** Strictly conform to WordPress PHP coding standards. Prefix all custom functions and namespaces with `eka_` to avoid conflicts.
* **No Inline Styles:** HTML files, block patterns, and template parts must not use inline `style="..."` attributes. Instead, layout structure should rely on semantic class names, structural Gutenberg blocks, and SCSS stylesheet definitions.
* **CSS & RTL:** Compiled SCSS with separate standard layout definitions and `rtl.css` compiled targets.
* **Nginx Configuration:** Standardize proxy pass configurations inside server blocks with comments explaining the bypass targets.

*Sample custom CPT registry inside theme:*
```php
// inc/custom-features.php
function eka_register_neofos_cpt() {
    $args = array(
        'label'        => __('Νέο Φως (Newsletter)', 'ekalexandria'),
        'public'       => true,
        'has_archive'  => true,
        'supports'     => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest' => true, // Enables Gutenberg block editor
        'menu_icon'    => 'dashicons-email-alt',
    );
    register_post_type('neo_fos', $args);
}
add_action('init', 'eka_register_neofos_cpt');
```

---

## 6. Testing & Verification Strategy
Before the migration is marked as successful, the following checks must pass:
* **Dynamic Proxy Pass Resolution:** 
  1. Retrieve an active attachment filepath from the database (e.g. `wp db query "SELECT meta_value FROM wp_postmeta WHERE meta_key='_wp_attached_file' LIMIT 1"`).
  2. Request that specific path under the staging URL `https://backstage.ekalexandria.org/wp-content/uploads/` via Nginx.
  3. Ensure it transparently falls back and renders the asset from `https://ekalexandria.org/wp-content/uploads/` with status code 200.
* **SSL Validation:** Ensure the local site resolves securely at `https://backstage.ekalexandria.org` using certificates generated by openssl.
* **RTL layout rendering:** Accessing Arabic page templates must load with `dir="rtl"` in the root HTML tag and mirror elements cleanly.
* **WPBakery Code Conversion:** The Home page ("Αρχική", ID `13236`) and News page ("Νέα", ID `8934`) must render cleanly using native Gutenberg blocks, removing all traces of WPBakery `[vc_*]` shortcodes.
* **Neo Fos RSS Automated Feed:** Accessing `https://backstage.ekalexandria.org/feed/neo-fos/` must return valid XML containing the latest Custom Post Type entries with PDF attachment mappings, confirming Mailchimp integration compatibility.
* **Lighthouse Performance Targets:**
  * **Desktop:** Mobile performance score `>= 80`, Desktop performance score `>= 95`.
  * **SEO & Accessibility:** Scores `>= 90`.

---

## 7. Boundaries
* **Always:**
  * Keep backups of database exports outside the public directories, named with the exact timestamped pattern `YYYY-MM-DD-HHMMSS-db_name.sql`.
  * Execute data operations and updates via WP-CLI with `--skip-plugins --skip-themes` to avoid PHP runtime exceptions from obsolete active code.
* **Ask First:**
  * Modifying shared server Nginx permissions.
  * Any database table adjustments beyond standard WordPress Core requirements.
* **Never:**
  * Connect the staging site configuration directly to the live production database.
  * Download or copy the live 50 GB `/wp-content/uploads/` directory to local storage.
  * Use inline HTML style declarations inside custom block patterns.
  * Deactivate or delete the legacy page builders before content conversion mappings are generated.
  * Commit third-party WordPress vendor files or database SQL files to the Git history.

---

## 8. Success Criteria
1. The local staging server `backstage.ekalexandria.org` successfully loads under PHP 7.4 (initially) and then PHP 8.3/8.4 over HTTPS (using openssl certs) and runs on the latest WordPress core release.
2. The custom block theme `ekalexandria-flagship` controls design styles dynamically using `theme.json` without layout builder bloat.
3. Legacy slider contents (from LayerSlider ID 6) on the Home page and News page are successfully recreated using native Gutenberg Cover/Slider blocks.
4. The admin panel is fully localized to Greek and customized with community branding.
5. The "Neo Fos" Custom Post Type works seamlessly, allowing PDF uploads and rendering cleanly for users.
6. The repository contains only the clean custom files under version control.
