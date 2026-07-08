# Task List: EKA Portal News Section Optimization

## Phase 1: Backend Foundations & Logic
- [ ] **Task 1.1:** Implement 30-Day Sticky Expiration & Announcement Pinning (`inc/news-logic.php`)
- [ ] **Task 1.2:** Filter native `core/post-date` block for Dynamic Date Formatting (`inc/news-logic.php`)
- [ ] **Human Verification 1:** Manually verify data querying logic and custom data formatting on the frontend. Await human approval.
- [ ] **Git:** `git add . && git commit -m "feat(news): implement backend foundations and logic"`

## Phase 2: UI Foundations (Styles & Scripts)
- [ ] **Task 2.1:** Integrate Swiper.js & scaffold SCSS (`functions.php`, `news.scss`, `news-carousel.js`)
- [ ] **Human Verification 2:** Verify asset delivery and initial styles via browser inspection. Await human approval.
- [ ] **Git:** `git add . && git commit -m "feat(news): integrate Swiper.js and scaffold SCSS"`

## Phase 3: FSE Components
- [ ] **Task 3.1:** Build News Hero Carousel Template Part (`parts/news-hero-carousel.html`)
- [ ] **Task 3.2:** Build News Query Loop Template Part (`parts/news-query-loop.html`)
- [ ] **Human Verification 3:** Verify complete FSE blocks in isolation via the Site Editor. Await human approval.
- [ ] **Git:** `git add . && git commit -m "feat(news): build hero carousel and query loop template parts"`

## Phase 4: Page Assembly & Filtering
- [ ] **Task 4.1:** Build Filter System & AJAX endpoint (`inc/news-filters.php`, `news-filters.js`)
- [ ] **Task 4.2:** Unify Templates (`home.html`, `archive.html`, `category.html`)
- [ ] **Human Verification 4:** Final review, cross-device testing, and Lighthouse audit. Await human approval.
- [ ] **Git:** `git add . && git commit -m "feat(news): assemble pages and implement AJAX filtering"`
