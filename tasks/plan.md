# Implementation Plan: Legacy WordPress Modernization for ekalexandria.org

## Overview
This plan outlines the systematic modernization of `ekalexandria.org` on a local staging environment `https://backstage.ekalexandria.org` running initially under **PHP 7.4** (transitioning to **PHP 8.3/8.4 (LTS)** after legacy page builder plugin deactivation) and **WordPress 6.x**. The project replaces the legacy WPBakery Builder (`js_composer`) and `LayerSlider` with a custom block theme named `ekalexandria-flagship` (cloned from [flagship](https://github.com/alexseif/flagship)). The custom layouts, custom post type `neo_fos`, custom RSS feeds, and admin panel overrides are coded directly inside the theme directory (`ekalexandria-flagship`) to maintain a single self-contained repository under git control. Media uploads are dynamically proxied via Nginx to bypass the 50 GB local download, and local SSL is generated using `openssl`.

---

## Architecture Decisions
1. **Theme-Driven Customizations:** To ensure simple deployment and clean tracking, CPT registrations, custom RSS templates, and admin styles are written in `inc/custom-features.php` inside the theme folder, eliminating the need for a separate custom plugin.
2. **Separation of Styling (SCSS) and Block Structure:** No styles will be hardcoded inside HTML block templates or inline properties. Design styles will be handled entirely via compiled SCSS files (`style.css` and `rtl.css` for multilingual support) to ensure block editor integrity.
3. **Reverse Proxying for Media Uploads:** To bypass downloading the 50 GB uploads folder from production, the local Nginx configuration will intercept requests to `wp-content/uploads/` and dynamically proxy missing assets to the live site.
4. **No-API Mailchimp Integration:** Mailchimp will query a dedicated RSS feed exposed for the `neo_fos` CPT (e.g. `https://backstage.ekalexandria.org/feed/neo-fos/`). This avoids maintaining fragile Mailchimp API tokens in the legacy database.
5. **Local SSL Setup:** Running local staging strictly over HTTPS (via `openssl` self-signed certs) prevents mixed content issues and ensures proper loading of Gutenberg blocks and REST resources.
6. **Project-Scoped Agent Guidelines:** A guiding principle rule file [AGENTS.md](file:///var/www/ekalexandria.org/.agents/AGENTS.md) is created to keep all future AI subagents aligned on these architectural decisions.

---

## Token Cost Estimation & Optimization Policies
To optimize developer cost, tasks will be run using a mix of local models (via `aider` or `ollama`) and API models (Gemini 3.5 Flash).

### Token Cost Projections

| Phase / Task Type | Est. Runs | Model Recommendation | Est. Input Tokens / Run | Est. Output Tokens / Run | Est. API Cost (Gemini 3.5 Flash) | Est. API Cost (High Reasoning) |
|---|---|---|---|---|---|---|
| **Phase 1: Configs & DB Setup** | 3 | Gemini 3.5 Flash | 25,000 | 1,500 | ~$0.012 | ~$0.25 |
| **Phase 2: Theme Setup & SCSS** | 3 | Gemini 3.5 Flash | 30,000 | 2,000 | ~$0.012 | ~$0.24 |
| **Phase 3: Core Theme Features** | 2 | Gemini 3.5 Flash | 35,000 | 2,500 | ~$0.010 | ~$0.20 |
| **Phase 4: Content Parsing / Regex** | 2 | High Reasoning | 50,000 | 3,000 | ~$0.020 | ~$0.60 |
| **Phase 5: Gutenberg Rebuilds** | 2 | High Reasoning | 60,000 | 4,000 | ~$0.025 | ~$0.80 |
| **Phase 6: QA, RTL & Polish** | 3 | Gemini 3.5 Flash | 40,000 | 2,000 | ~$0.015 |  ~$0.35 |
| **Total Projected Project Cost** | **15 Runs** | **Hybrid Orchestration** | **~550,000** | **~32,000** | **~$0.09** | **~$2.44** |

### Guidelines to Minimize Token / Cost Consumption
1. **Selective Context Parsing:** Never dump database SQL file outputs, HTML page source codes, or compiled stylesheets directly into the prompt. Pass only configuration headers, JSON structure outlines, and PHP class interfaces.
2. **Local Aider-Ollama Target:** For standard file manipulation, terminal tasks, and config compilation (e.g. Phase 1 & 2), utilize a local lightweight model to zero out API costs.
3. **Reasoning Redirection:** Only redirect tasks to reasoning models when converting complex serialized shortcode lists to nested HTML or resolving PHP 8.3/8.4 syntax deprecation blocks.

---

## Risks and Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| Nginx reverse proxy CORS block | Medium | Configure headers inside the proxy block to allow cross-origin requests from `https://ekalexandria.org` to staging. |
| Incomplete WPBakery conversions | High | Write a PHP database parser that replaces standard `[vc_*]` grid tags with plain layout blocks, and test it incrementally on single draft pages before updating the live database table. |
| PHP 8.3 Fatal Errors in old plugins | High | Run PHPStan or quick WP-CLI syntax audits. Disable offending secondary legacy plugins and update core plugins. |
| RTL styles breaking layouts | Medium | Separate mobile-first base SCSS from layout adjustments, using dedicated `rtl.scss` structures loaded only when Polylang detects RTL locales (Arabic). |

---

## Open Questions
* **RTL styles source:** Do we have existing style elements from Betheme we must map to `ekalexandria-flagship`'s RTL layouts, or do we construct them cleanly from scratch?
