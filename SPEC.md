# Specification: EKA Portal News Section Optimization (Task 5.3)

## 1. Objective and Target Users
**Objective:** To modernize, standardize, and optimize the News Section of the EKA Portal. This involves creating a unified, high-performance listing experience that leverages native FSE (Full Site Editing) Query Loop blocks. The page will highlight the 5 most recent news items in a hero carousel and list subsequent news in sleek, modern rows. 
**Target Users:** Community members and site visitors seeking the latest news, announcements, and historical updates.

## 2. Core Features and Acceptance Criteria
*   **Hero Carousel:** The top of the news page will feature a lightweight carousel displaying the 5 latest news posts. This operates completely independently of the main list below it (intentional duplication of recent posts is expected).
*   **Unified Template:** The layout (hero + listing) will remain identical across the main News page, Archive pages, and Category pages.
*   **Dynamic "Sticky" Logic:** Posts marked as sticky OR categorized as "Announcements" will remain pinned to the top of the listings, but this status will automatically expire after 30 days from publication.
*   **Modern Row Layout:** News items will be displayed in enticing horizontal rows containing:
    *   Featured Image
    *   Title
    *   Category
    *   Excerpt
    *   Dynamic Date: Displays as "X days ago" if published within the last 21 days; otherwise, it displays the standard absolute date.
    *   Native Share Button
    *   *Note: Author will NOT be displayed.*
*   **Interactive Listing:** The main list will include pagination and a filtering system allowing users to filter by Category, Year, and Month. The filtering will utilize an SEO-friendly AJAX architecture (updating the URL History API) with a sleek progress loader under the top bar to avoid hard page reloads.
*   **SEO & GEO Optimization:** High SEO utility and geographic targeting considerations integrated natively into the markup.
*   **Multilingual Scope:** Fully integrated with the site's language configuration. Every language version must load and filter news strictly in its respective language.
*   **Responsive & Mobile-First:** The design architecture must be mobile-first, ensuring the carousel, news rows, and filters are fully responsive across all device breakpoints.
*   **Performance Metrics:** The implementation is expected to achieve a high Google PageSpeed score by relying on lightweight FSE structures and avoiding heavy, render-blocking scripts.

## 3. Project Structure & Architecture
*   **Templates (`templates/`):** 
    *   `archive.html` / `category.html` / `home.html` (News index): All will rely on the same underlying structure.
*   **Template Parts (`parts/`):**
    *   `news-hero-carousel.html`: Dedicated part for the top 5 latest news.
    *   `news-query-loop.html`: The main FSE Query Loop block structure for the rows.
*   **PHP Logic (`inc/custom-features.php` or `inc/news-logic.php`):**
    *   Filter hooks to handle the 30-day sticky expiration (`pre_get_posts`).
    *   Custom function/shortcode or block render filter to output the 21-day relative date format.
    *   Logic to render the Category/Year/Month filters (likely via a custom lightweight shortcode or block accompanying the native Query Loop).
*   **Styles (`assets/scss/`):** SCSS to style the "sleek rows" and the hero carousel.

## 4. Code Style & Implementation Details
*   **FSE Native First:** Maximize the use of the core `core/query` block. We will avoid relying on heavy third-party plugins.
*   **PHP Interventions:** PHP will only be used where native FSE falls short, specifically for:
    *   Modifying the query query vars (for the 30-day sticky logic).
    *   Dynamically rendering the date format.
    *   Building the frontend filters (Category/Date) since native FSE does not yet have robust faceted search built-in.
*   **Performance:** The carousel will utilize Swiper.js to avoid reinventing the wheel while maintaining a lightweight structure without bundle bloat.

## 5. Testing Strategy
*   **Sticky Expiration:** Create a test post in "Announcements" older than 30 days and verify it no longer sticks to the top.
*   **Dynamic Date Formatting:** Verify a post published 20 days ago shows relative time, and a post from 22 days ago shows the exact date.
*   **Unified UI:** Navigate between the main news page and a specific category page to ensure the layout (carousel + rows) remains identical.
*   **Filter Integrity:** Test the category and date filters to ensure they correctly update the paginated results without breaking the layout.
*   **Multilingual Integrity:** Switch between languages (e.g., Greek, English, Arabic) and verify that the query loop strictly surfaces news corresponding to the active language.
*   **Performance Audit:** Run Lighthouse/PageSpeed Insights on the news listing page to validate the high page speed expectation.

## 6. Boundaries
*   **Always Do:** Keep the UI strictly consistent across all news-related routes. Use native FSE HTML markup for the rows.
*   **Ask First:** If implementing the filter system requires a complex custom block vs. a simple PHP-rendered shortcode placed above the query loop.
*   **Never Do:** Never display the author metadata. Do not introduce heavy page builder plugins or bloated slider plugins to achieve the layout.
