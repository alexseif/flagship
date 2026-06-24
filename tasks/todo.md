# Ekalexandria Phase 2: Tasks

*Reference: See `tasks/legacy_data.md` for exact naming and ID mapping of menus and sliders.*

- [ ] **Slice 1: Global Elements & Multilingual Headers (Branch: `feature/slice-1-navigation`)**
  - [ ] `git checkout -b feature/slice-1-navigation`
  - [ ] Create language-specific header parts: `header-el.html`, `header-en.html`, `header-ar.html`.
  - [ ] Create language-specific footer parts: `footer-el.html`, `footer-en.html`, `footer-ar.html`.
  - [ ] Add native Navigation block placeholders to the headers mapped to the extracted Main Menus (e.g., "Main Greek Menu", "Main English Menu").
  - [ ] Add native Navigation block placeholder to the Greek footer mapped to "Footer Greek Menu".
  - [ ] Verify visual parity and structure.
  - [ ] `git commit -am "feat: implement language-specific headers and footers with menu placeholders"`
  - [ ] `git checkout main && git merge feature/slice-1-navigation`

- [ ] **Slice 2: Custom Post Types (Branch: `feature/slice-2-cpts`)**
  - [ ] `git checkout -b feature/slice-2-cpts`
  - [ ] Register `neo_fos` CPT for newsletters.
  - [ ] Create and run script to extract legacy Neo Fos content.
  - [ ] Register Board Members CPT/structure and extract data.
  - [ ] Verify CPTs and data in WP Admin.
  - [ ] `git commit -am "feat: register CPTs and extract legacy data"`
  - [ ] `git checkout main && git merge feature/slice-2-cpts`

- [ ] **Slice 3: Language-Specific Core Templates (Branch: `feature/slice-3-templates`)**
  - [ ] `git checkout -b feature/slice-3-templates`
  - [ ] Build language-specific page templates: `page-el.html`, `page-en.html`, `page-ar.html` (including their respective headers/footers).
  - [ ] Build `single-neo_fos.html` template.
  - [ ] Build `archive-neo_fos.html` template.
  - [ ] Build `archive.html`, `search.html`, and `404.html` templates.
  - [ ] Verify templates load without Block Editor errors.
  - [ ] `git commit -am "feat: implement language-specific FSE page templates"`
  - [ ] `git checkout main && git merge feature/slice-3-templates`

- [ ] **Slice 4: Dynamic Loops & Static Gallery Placeholders (Branch: `feature/slice-4-placeholders`)**
  - [ ] `git checkout -b feature/slice-4-placeholders`
  - [ ] Implement native Gutenberg dynamic Query Loop block for the Homepage to pull the latest 5 News/Ανακοινώσεις posts.
  - [ ] Implement the same dynamic Query Loop block for the News Page.
  - [ ] Insert native Gutenberg placeholder blocks (e.g., `core/gallery`) for specific page inner-sliders (Staff, Greek Club, Cemeteries, Music Museum, Science Museum, Cemeteries Conservation).
  - [ ] `git commit -am "feat: implement dynamic news loops and static gallery placeholders"`
  - [ ] `git checkout main && git merge feature/slice-4-placeholders`

- [ ] **Slice 5: Homepage Widgets & Page Recoveries (Branch: `feature/slice-5-page-recovery`)**
  - [ ] `git checkout -b feature/slice-5-page-recovery`
  - [ ] Rebuild homepage widgets (Υπηρεσίες, Ιστορία, Ι.Ν. Ευαγγελισμού) using native columns/groups in `front-page.html`.
  - [ ] Insert native Navigation placeholders for nested in-page sub-menus on Establishment, Activities, and Services parent/child pages (mapping to the respective language menus).
  - [ ] Restore partner logos on `/el/διάφορα/σύνδεσμοι/`.
  - [ ] Restore icons on `/el/ανακοινώσεις-νέα/ανακοινώσεις-εκα/`.
  - [ ] `git commit -am "feat: recover specific page widgets and in-page nested menus"`
  - [ ] `git checkout main && git merge feature/slice-5-page-recovery`

- [ ] **Slice 6: Deployment Prep (Branch: `feature/slice-6-sanitization`)**
  - [ ] `git checkout -b feature/slice-6-sanitization`
  - [ ] Write WP-CLI cleanup script for legacy plugin tables (WPBakery, LayerSlider, RevSlider).
  - [ ] Test cleanup script safely on local staging database.
  - [ ] `git commit -am "chore: create and test DB sanitization scripts"`
  - [ ] `git checkout main && git merge feature/slice-6-sanitization`
