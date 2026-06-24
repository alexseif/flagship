# Ekalexandria Phase 2: Tasks

*Reference: See `tasks/legacy_data.md` for exact naming and ID mapping of menus and sliders.*

- [x] **Slice 1: Global Elements & Multilingual Headers (Branch: `feature/slice-1-navigation`)**
  - [x] `git checkout -b feature/slice-1-navigation`
  - [x] Create language-specific header parts: `header-el.html`, `header-en.html`, `header-ar.html`.
  - [x] Create language-specific footer parts: `footer-el.html`, `footer-en.html`, `footer-ar.html`.
  - [x] Add native Navigation block placeholders to the headers mapped to the extracted Main Menus (e.g., "Main Greek Menu", "Main English Menu").
  - [x] Add native Navigation block placeholder to the Greek footer mapped to "Footer Greek Menu".
  - [x] Verify visual parity and structure.
  - [x] `git commit -am "feat: implement language-specific headers and footers with menu placeholders"`
  - [x] `git checkout main && git merge feature/slice-1-navigation`

- [x] **Slice 2: Custom Post Types (Branch: `feature/slice-2-cpts`)**
  - [x] `git checkout -b feature/slice-2-cpts`
  - [x] Register `neo_fos` CPT for newsletters.
  - [x] Create and run script to extract legacy Neo Fos content.
  - [x] Register Board Members CPT/structure and extract data.
  - [x] Verify CPTs and data in WP Admin.
  - [x] `git commit -am "feat: register CPTs and extract legacy data"`
  - [x] `git checkout main && git merge feature/slice-2-cpts`

- [x] **Slice 3: Language-Specific Core Templates (Branch: `feature/slice-3-templates`)**
  - [x] `git checkout -b feature/slice-3-templates`
  - [x] Build language-specific page templates: `page-el.html`, `page-en.html`, `page-ar.html` (including their respective headers/footers).
  - [x] Build `single-neo_fos.html` template.
  - [x] Build `archive-neo_fos.html` template.
  - [x] Build `archive.html`, `search.html`, and `404.html` templates.
  - [x] Verify templates load without Block Editor errors.
  - [x] `git commit -am "feat: implement language-specific FSE page templates"`
  - [x] `git checkout main && git merge feature/slice-3-templates`

- [x] **Slice 4: Dynamic Loops & Static Gallery Placeholders (Branch: `feature/slice-4-placeholders`)**
  - [x] `git checkout -b feature/slice-4-placeholders`
  - [x] Implement native Gutenberg dynamic Query Loop block for the Homepage to pull the latest 5 News/Ανακοινώσεις posts.
  - [x] Implement the same dynamic Query Loop block for the News Page.
  - [x] Insert native Gutenberg `core/gallery` blocks programmatically populated with the exact Image IDs listed in `legacy_data.md` for the inner-page galleries (Staff, Greek Club, Cemeteries, Music Museum, Science Museum, Cemeteries Conservation).
  - [x] `git commit -am "feat: implement dynamic news loops and static gallery placeholders"`
  - [x] `git checkout main && git merge feature/slice-4-placeholders`

- [x] **Slice 5: Homepage Widgets & Page Recoveries (Branch: `feature/slice-5-page-recovery`)**
  - [x] `git checkout -b feature/slice-5-page-recovery`
  - [x] Rebuild homepage widgets (Υπηρεσίες, Ιστορία, Ι.Ν. Ευαγγελισμού) using native columns/groups in `front-page.html`.
  - [x] Insert native Navigation placeholders for nested in-page sub-menus on Establishment, Activities, and Services parent/child pages (mapping to the respective language menus).
  - [x] Restore partner logos on `/el/διάφορα/σύνδεσμοι/`.
  - [x] Restore icons on `/el/ανακοινώσεις-νέα/ανακοινώσεις-εκα/`.
  - [x] `git commit -am "feat: recover specific page widgets and in-page nested menus"`
  - [x] `git checkout main && git merge feature/slice-5-page-recovery`

- [ ] **Slice 6: Deployment Prep (Branch: `feature/slice-6-sanitization`)**
  - [ ] `git checkout -b feature/slice-6-sanitization`
  - [ ] Write WP-CLI cleanup script for legacy plugin tables (WPBakery, LayerSlider, RevSlider).
  - [ ] Test cleanup script safely on local staging database.
  - [ ] `git commit -am "chore: create and test DB sanitization scripts"`
  - [ ] `git checkout main && git merge feature/slice-6-sanitization`
