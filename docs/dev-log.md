# Development log

A running log of how the real site is being built: what was done, why, how, and what to watch out for. It is written while coding so that nothing is lost, and is the raw material for the proper documentation later.

- Newest entries at the bottom. One entry per step or notable change.
- Decisions agreed with the team live in `docs/decisions.md`. This log records the choices made while implementing them, the problems found, and the reasons behind the code.
- Each entry: **What**, **Why / how**, **Gotchas**, **Open** (things left for later).

---

## 2026-11-15 · Download of the old sites (`bin/fetch`)

**What:** `bin/fetch` downloads it-tour.bg and the six city domains into `data/import/` (not committed): `pages/<domain>/...html`, `files/<path>` for uploads, `files/external/<host>/<path>` for sponsor logos hosted elsewhere, and `manifest.json` (URL → status, type, file, time).

**Why / how:**
- Download once, then import offline as often as needed, without hitting the old server again.
- Starts from each homepage and follows only links to `/`, `/archive/<year>` and `/speakers/<id>` on our domains. Speaker profiles are the same on every domain, so each id is fetched once (from the first domain it was seen on).
- Images: every `/system/uploads/...` reference (`src`, `href`, CSS `url()`), plus slide files (pdf, ppt…) on the old hosts, plus `<img>` in the sponsors section on other hosts.
- For `thumb_` files it tries the original first (drop the `thumb_` prefix), then the thumbnail.
- Polite: one request at a time, 300 ms pause, 3 retries on 5xx. Re-runs skip what's in the manifest; `--refresh` re-downloads pages.

**Gotchas:**
- Some upload names contain spaces; they are requested as `%20`. The file is saved under the name as it appeared in the HTML (one has a literal `%20` in its saved name; `bin/import` tries both forms).
- No sitemap exists (it-tour.bg/sitemap.xml returns the 404 page with status 200), so pages are found by following links.
- Result (2026-11-15): 290 pages (7 homepages, 62 editions, 221 profiles), 603 files. Lost already on the old side: VarnaConf 2012 slides `dimitar.pptx`, `vasil.pdf`, `hristo.html`; sponsor logos of BurgasLab (Facebook CDN, 403) and VarnaLab (404).

## 2026-11-15 · Database schema and import (`bin/import`)

**What:** `migrations/001_initial.sql` (the data model from decisions.md), `src/Db.php`, `src/Text.php`, `src/Media.php`, `bin/migrate`, `bin/import`.

**Why / how:**
- `bin/import` always starts clean: deletes `data/site.sqlite`, `data/media/*`, `public/media/*`, `public/files/*`, runs migrations, then reads `data/import/`. About 50 s, mostly image resizing.
- Speakers keep their old numbers as `speakers.id`, so `/speakers/<id>` keeps working. Bio and links come from the profile page (latest bio); the edition pages are only a fallback.
- Editions: name and logo from the edition's own entry in the "Архив" list on the page; colour from `nav { border-top-color }`; date parsed from "17 Август 2024"; venue and after party from their `article` blocks (name, link, address, second `address` as directions, map embed URL). Coordinates are taken from the embed URL (`!2d<lng>!3d<lat>` or `ll=`; `sll=` is a default left in old links and is ignored).
- Schedule: each `#schedule ol` is a list; each `li` gives time, title, speakers (`a.speaker` → `#speaker-ID`), description (children of `details` other than `summary`), video and slides links.
  - A slot is a talk if it has speakers, or (not a break, not opening/closing) has a video, slides or description. Breaks: titles starting кафе, обяд, обедна, почивка, break, coffee, lunch. Openings and closings are always plain slots (agreed: their 34 old videos are dropped).
  - The after party slot is dropped from the schedule (agreed); its time goes to `events.party_starts_at`.
  - A slot ends when the next slot in the same list starts. VarnaConf 2015 and 2016 have two lists (talks and workshops) that overlap; breaks repeated in both lists are kept once.
- Sponsors: the old sponsors section is hand-written HTML. Headings (h2–h4) give the type ("Златни"…), each `<img>` is a sponsor (name from `alt`, link from the surrounding `<a>`). Sponsors are matched across editions by lowercase name; later editions update the logo and link.
- Links: broken old forms like `https://twitter.com/https://x.com/name` are cleaned to the inner URL; YouTube links normalised to `https://www.youtube.com/watch?v=ID`.
- Slides hosted on the old sites are copied to `public/files/<slug>-<hash8>.<ext>` and the talk points there.
- Images (`src/Media.php`): original kept in `data/media/<sha1>.<ext>`; public WebP copies at 400/800/1600 px (only widths up to the original; smaller images get one copy at their own size) named `<slug>-<hash8>-<width>.webp`. Speaker photos get a centred square crop. EXIF rotation is applied; re-encoding drops metadata. SVG is checked for scripts and published as is. The same content is stored once (hash).
- Search text: lowercase title and names plus their Latin transliteration (`Text::searchText`).
- The report lists counts per edition, totals, possible duplicate speakers and warnings.

**Gotchas:**
- Use the largest image in an upload folder, not the one the page references: pages reference `thumb_image` and `main_image` (250 px); the original is `image.*` (logos ~2000 px). Sponsor logos share one folder, so for them the exact file is used.
- `Dom\HTMLDocument` (PHP 8.4+) is used for parsing; its `querySelectorAll` does not support `:scope`, so list items are taken from `$ol->children`.
- libpng prints "Interlace handling should be turned on" warnings for a few PNGs; harmless.
- Result (2026-11-16): 62 events, 458 talks (201 with video), 790 slots, 221 speakers, 37 sponsors, 126 sponsorships, 352 media. 106 MB of originals → 16 MB WebP. 27 speakers have no photo on the old site either.
- Same-name speakers Тодор Тодоров (#20, #172), Иван Димитров (#23, #104), Николай Маринов (#114, #115) were checked by the team: different people. Listed in `NOT_DUPLICATES` so the report stays quiet.

**Open:**
- Event photo galleries (13 old editions, 63 photos downloaded) are not imported; the photo section is parked.
- "Димитър Иорданов" (BurgasConf 2019) is a talk whose title is a person's name, probably a data mistake in the old site.
- Two 2019 lightning talks are separate talks overlapping the "Lightning Talks" slot; left as they are.

## 2026-11-16 · Step 1: skeleton of the public site

**What:** entry point, site detection, router, views, layout, 404 and error pages, missed-address log, security headers, local server, checks and unit tests.

**Why / how:**
- Request flow: `public/index.php` → `App::handle(Request): Response` → `Site::fromHost()` → `App::routes($site)` (a `Router` per site type) → handler returns a `Page` (template + plain data) or a `Response` → `View::page()` renders the template, then `templates/layout.php` around it (or only the `partial` template when the request has `X-Partial: results`).
- `App::handle` is a pure function of the request (plus database and clock), and the body is a finished string. This is what a page cache can wrap later.
- Settings per installation in `config.php` (not committed; `config.example.php` is). `main_host` and `city_host` (pattern with `{city}` = production domain without `.com`). `Site` uses it both ways: host → site, and site → absolute URLs. Every link to another site is absolute and built by `main_url()` / `city_url()`.
- Table keys for a site use the production domain (`Site::domain()`: `it-tour.bg` or `varnaconf.com`), so redirects and missed addresses are the same on every installation.
- Templates: plain PHP. Print only through `e()`; `partial()` and `raw()` for finished HTML. `bin/check` fails otherwise. Conditional attributes use `<?php if ?>` instead of `<?= cond ? ... ?>` for that reason.
- Colours: city sites set `--c` (latest published edition's colour) and `--c-ink` (near-black or white, whichever contrasts more, `ink_for()`) on `<html class="city">`; the main site sets `--accent` from the `accent` setting on `<html class="hub">`. `site.css` was adapted from F: the per-city `body[style*="--plovdiv"]` rules became generic `--c-ink` rules.
- Header menu, version 1: Спирки, Лекции, Лектори, Архив, Екип. "Абонирай се" only if `newsletter_url` is set. Footer: active cities (brand name from the latest edition: "VarnaConf"), main pages, contact email and social links from settings.
- Default settings in `migrations/002_default_settings.sql`.
- 404: redirects table first, then the 404 page with this season's events and links. Missed addresses go to `missed_urls` (hits, last seen), at most 10,000 rows so bots can't grow it forever.
- Errors: PHP warnings become exceptions; any exception is logged (`Log`, daily files, 10 MB/day cap with a single "LOG FULL" line, files older than 30 days deleted now and then) and the standalone `templates/error.php` is shown (status 500). `debug` in config shows the error instead.
- Headers on every response: CSP (own scripts and styles only; `style-src-attr 'unsafe-inline'` for the `style="--c:…"` colour attributes; frames only YouTube and Google Maps), nosniff, Referrer-Policy, Permissions-Policy; `X-Robots-Tag: noindex` outside production.
- `www.` is redirected in PHP too, in case the server doesn't.
- Fonts: Montserrat self-hosted as a variable font (400–800), split into latin, latin-ext and cyrillic files from Google Fonts; plus the wordmark font from the design.
- Local: `bin/serve` runs `php -S localhost:8000 -t public public/index.php`; browsers send any `*.localhost` to this computer, so `varnaconf.localhost:8000` works without hosts-file changes. The router script lets the built-in server serve real files itself.

**Gotchas:**
- Inline `<style>` is blocked by our CSP, so even the error page uses a file (`public/assets/error.css`).
- Montserrat shows Bulgarian letter forms (д like g, т like m) because the page is `lang="bg"`. The F mockup looks the same, so this is intended.
- Official transliteration: "ия" at the end of a word is "ia" (история → istoria), and the law's one exception is България → Bulgaria; the exception must run before the "ия" rule.
- The router's pattern syntax allows `{name:regex}` with `{n}` quantifiers inside, such as `{year:\d{4}}`.

## 2026-11-16 · Step 2: test setup

**What:** Playwright browser tests (`tests/browser/`), frozen test database, fake time, `bin/test` running everything.

**Why / how:**
- `bin/test`: `bin/check`, PHP unit tests (`tests/unit/*.php`, each returns `name => closure`, using `same()` and `ok()`), then `npx playwright test`. `--php` skips the browser tests; arguments after `--` go to Playwright.
- Playwright starts its own server on port 8001 with `IT_TOUR_CONFIG=tests/config.php`, `IT_TOUR_DB=data/test/site.sqlite` and `IT_TOUR_TESTING=1`.
- `bin/test-db` freezes `data/site.sqlite` into `data/test/site.sqlite` (`VACUUM INTO`). Screenshots and content tests rely on it; re-freeze deliberately after import changes and re-approve screenshots. Images are shared through `public/media` (names are content hashes).
- Time: `Clock::now()` reads, in order, `Clock::fake()` (set from the `X-Test-Now` header only when `IT_TOUR_TESTING=1`), `IT_TOUR_NOW`, config `now`, the real time. Tests default to `2026-11-16 12:00` and use `at(page, when)` to change it.
- Two projects: desktop 1400×900 and phone 390×844. Each page test checks content, no sideways scroll (`expectNoSidewaysScroll`), axe WCAG 2.2 AA (`expectAccessible`) and a full-page screenshot.
- Approved screenshots are committed in `tests/browser/screenshots/`; `bin/test -- --update-snapshots` approves changes.
- Playwright uses its own Chromium build (installed into `~/.cache/ms-playwright` by `bin/test` the first time), so screenshots don't change when the system Chrome updates.

**Gotchas:**
- Role-based locators skip hidden elements: the main menu is hidden on phones, so menu tests use `#main-nav a`.
- The font preload must use the exact address the stylesheet uses (no `?v=`), otherwise the font is downloaded twice. The tests' network log showed it.
- `php -S` writes every request to stderr; the test server's output is ignored. PHP errors still go to `data/logs/`.

## 2026-11-16 · Step 3: the edition page on city sites

**What:** `/` and `/archive/<year>` on each city domain (`src/Pages/EventPage.php`, `templates/pages/event.php`), with queries in `src/Events.php`, `src/Programme.php`, `src/Sponsors.php`, and partials `img`, `place` (venue / after party) and `embed` (click-to-load YouTube or map).

**Why / how:**
- One page for all states; the state comes from `Events::state()`: upcoming before the start time, live until the end time, finished after (Bulgarian time; the after party doesn't count). Upcoming shows a countdown (server-rendered, updated by `site.js` every 30 s); live shows the live block (player, external-player link, now on stage) when a live or stream link is set, and marks the current and past slots; finished shows counts of talks, speakers and videos.
- `/` shows the latest published edition; `/archive/<year>` any published edition. The latest edition's canonical is `/` (it answers at both); others are canonical to their `/archive/<year>`.
- **Each edition uses its own year's colour** (page data `colour`, used by the layout instead of the city's latest colour), and its own imported logo. The imported logos are already the per-year badges with the year in them.
- Sections appear only when they have content; the section bar lists only those present. The after party is its own section (only when visible), not in the schedule or venue.
- Schedule: talk titles link to talk pages on the main site (built in step 5), speakers to `/speakers/<id>` on the same city site. Descriptions are cut to about 220 characters (`excerpt()`). Slots starting at the same time get a small "паралелно" label (VarnaConf 2015/2016 workshops). English titles get `lang="en"` automatically (`lang_attr()`: no Cyrillic letters).
- Speakers block: from the event's talks, in programme order, each once, with their talk titles. No photo: a round initial.
- Sponsors grouped by sponsorship type in sponsorship order; logos link to the sponsor.
- Maps and videos load only on click (`partials/embed.php` + `site.js`); without JavaScript the button is a link. "Open in Google Maps" uses a Google Maps search for name + address, because embed addresses can't be turned into normal map links reliably.
- Call for speakers section shows while `cfs_deadline` hasn't passed; it has no button yet because the speaker form is undecided.
- Import change: `events.ends_at` is now 18:00 or the last slot's start, whichever is later.
- Media rows joined into queries use `Media::JOIN_COLUMNS` and `Media::pluck($row, 'photo')`.

**Gotchas:**
- **Accessibility fixes to the F design** (axe found them): `--dim` darkened from `#7f8899` to `#5f6878` (small grey text was 3:1, a design colour, not an editor's). `<dl>` may only contain `dt`/`dd` groups, so the map button sits after it.
- **Colours chosen by editors are not corrected** (the user's instruction, 2026-11-16): city/edition colours and the hub accent are used as they are, and contrast problems are logged for review instead. An automatic darkening (`text_colour()`) was tried and removed. Two logs:
  - `bin/contrast-report` lists every edition colour and the accent with their contrast as link text (`color-mix(70%, #111)`, as site.css does) on both page grounds and as a button background. On 2026-11-16: 25 of 63 colours are below 4.5:1 somewhere (for example VarnaConf 2026 `#6dc4aa` text 3.2:1, TarnovoConf 2015 `#ffffff`).
  - The browser tests' axe check doesn't fail on `color-contrast`; it writes those findings to `tests/browser/reports/contrast.txt` (not committed), and `bin/test` prints how many there are.
- Lazy images with `height: auto` collapse to zero until loaded (logos were "hidden" and the page jumped). Sponsor logos have a fixed height.
- `mb_ucfirst()` (PHP 8.4+) capitalises Bulgarian dates ("Събота, 17 август 2024").
- PHP page tests (`tests/unit/PagesTest.php`) cover data the frozen database doesn't have: each test copies the test database, changes it with SQL, and calls `App::handle()` in-process (hidden after party, unpublished programme/edition/speaker, escaping).

**Open:**
- The old data has no live or stream links, so the live block is only covered by logic tests and the "now on stage" marking; check it with real links before the first live event.
- When a new year's badge is made from the city's SVG, the year text inside the badge must change too, not only the colour (admin feature).
- "I'm coming" and "add to calendar" are not built (parked / next version).
