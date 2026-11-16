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
- axe found that `<dl>` may only contain `dt`/`dd` groups, so the map button sits after it.
- **No colour is corrected for contrast, design colours included** (the user's instruction, 2026-11-16: colours are by design, to be discussed after the site is built). A darker `--dim` grey was tried and reverted; the design's `#7f8899` is about 3:1 on the page grounds and shows up in the contrast report.
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

## 2026-11-16 · Colour corrections reverted

The user decided (2026-11-16) that no colour is corrected for contrast for now, design colours included: colours are by design and are discussed after the site is built. `--dim` is back to `#7f8899`; the automatic darkening of city and accent colours was removed. `bin/contrast-report` and the browser tests' `tests/browser/reports/contrast.txt` log the problems (216 elements across the tested pages on 2026-11-16, mostly the dim grey and light city colours).

## 2026-11-16 · Step 4: archive of all editions

**What:** `it-tour.bg/archive` (`src/Pages/Archive.php`, `templates/pages/archive.php`, `Events::archive()`), the edition pages themselves were done in step 3.

**Why / how:**
- All published editions grouped by year, newest first; each card has the edition's logo, name, date and venue, and the numbers of talks and videos (counted from published talks). Photos were in the mockup's card but are parked.
- Filters: city pills are plain links (`?city=varna`), the year is a GET form (`?year=2015`); both combine. `site.js` hides the form's button and submits on change (`form[data-autosubmit]`). Unknown values are ignored rather than giving errors. The list is small (62), so no paging or partial rendering here.
- The current year's heading says how far the season is ("сезонът тече · 2 от 5 издания минаха") while some of its editions aren't finished.
- Cities without any published edition are left out of the pills; Благоевград stays (2013–2014).

**Gotchas:**
- `.btn { display: inline-flex }` beats the `hidden` attribute; a global `[hidden] { display: none !important; }` fixes it for every element.
- Playwright's `getByRole('link', { name: 'Варна' })` also matches "VarnaConf…" cards and footer links by substring; scope it to the pills group and use `exact: true`.

## 2026-11-16 · Step 5: speaker profiles and talk pages

**What:** `/speakers/<id>` on every domain (`src/Pages/SpeakerPage.php`, `templates/pages/speaker.php`), `/talks/<id>-<slug>` on the main site (`src/Pages/TalkPage.php`, `templates/pages/talk.php`), queries in `src/Talks.php` and `src/Speakers.php`, `partials/talk-card.php`; video stills stored in the media library.

**Why / how:**
- **A talk is public only when the talk, its edition and that edition's programme are all published** (`Talks::VISIBLE`). Otherwise its page is a 404 and it's left out of profiles.
- Talk addresses: the number finds the talk. `/talks/179` and `/talks/179-any-old-slug` redirect (301) to the current slug, so nothing needs storing when a title changes.
- Speaker profiles answer on every domain, as the old sites did, with the canonical on it-tour.bg; city copies keep the city's colours. Profile: photo (or a round initial), "Лектор на IT Tour · N лекции", bio paragraphs, links labelled by site (LinkedIn, GitHub, X… or the domain; `Speakers::linkLabel()`), upcoming talks as a highlighted block, past talks as cards, newest first.
- Talk page: title (marked English when it has no Cyrillic), edition tag, date and city, speakers, video (click to load), description, YouTube and slides buttons; side box with the speakers, the other talks of the edition in programme order and a link back to the edition. Upcoming talks say the video will come after the event. Non-YouTube videos (old `.avi` and `.mp4` on va.ludost.net) get a plain "Видео ↗" link.
- **Video stills are self-hosted:** `bin/fetch` downloads `maxresdefault.jpg` (or `hqdefault.jpg`) for every YouTube video once; `bin/import` adds them to the media library cropped to 16:9 (`Media::add()` now takes a crop ratio: 1.0 for speaker photos, 16/9 for stills) and stores them in `talks.video_still_media_id` (`migrations/003_video_stills.sql`). The step 3 embed loaded stills from `i.ytimg.com`, which contacted Google on page load; it now uses our copies, and `i.ytimg.com` is out of the CSP. A browser test checks that a talk page makes no request outside our site before the click.
- Talk cards without a video have no picture area, just the edition's colour as a top edge.

**Gotchas:**
- `/talks/{id}-{slug}` is matched before `/talks/{id}`; the slug pattern allows an empty slug (`/talks/179-`).
- The `<dl>`/link markup and lazy-image sizing rules from step 3 apply here too (fixed sizes on photos).

**Open (data):**
- **No videos are linked for 2018 or 2022–2026**, and 2019 has only 12. The recordings are on the YouTube channel but the old site never linked them; this is the "video matching" import task.
- **No talk descriptions before 2025**: the old site only has them for 2025 (25) and 2026 (19).
- There is one 2021 edition (10 talks), which the step 4 test data counts.

## 2026-11-16 · Video embeds: two buttons, no autoplay

**What:** `partials/embed.php` shows two buttons on a video still: "Гледай тук" (load the player here) and "Гледай в YouTube ↗" (a plain link). The player loads without `autoplay=1`. The separate YouTube button under the talk video and in the live block is gone (the still has it now). The live block's bar shows only when there's an external-player link.

**Why:** YouTube doesn't count autoplayed embeds as views (YouTube Help, "Embed videos & playlists"; the IFrame API docs say a play counts only when started with the player's own button). One click with autoplay lost the views; two clicks behind a single button felt broken. Two clearly labelled choices keep views counting, keep nothing going to Google before a click, and give a one-click way to YouTube (or its app on phones). The embedded player's YouTube logo still opens YouTube mid-talk.

**Tests:** the talk page test checks both links and that the iframe address has no autoplay; a PHP page test fakes a live event with live and stream links.

**Decided later the same day:** new talks' still images (see decisions.md): when an editor saves a talk with a YouTube link, the server downloads the still once; if that fails the card shows the edition colour and saving retries; editors can upload their own. Until the admin exists, a `bin/video-stills` command would do the same for talks missing a still.

## 2026-11-16 · Video matching

**What:** `bin/match-videos` matches talks without a video to the IT Tour YouTube channel and writes `import/video-matches.csv` (committed; see `import/README.md`). `bin/import` applies it; `bin/fetch` downloads stills for the matched videos.

**Why / how:**
- The channel's lists were saved with yt-dlp (installed in a throwaway virtualenv, not a project dependency) into `data/import/youtube/`: 722 uploads, 26 livestreams, 48 playlists. The commands are in the script's header.
- Per edition: candidates are the edition's playlist ("VarnaConf 2023"), or the whole channel when there is none (then the speaker must match). Video titles are split into a title and a speaker part after removing numbering ("07. ", "006 ") and edition suffixes; old playlists also use "Speaker - Title", "Speaker: Title" and "Title Speaker". Titles are compared lowercased and transliterated (`similar_text`); surnames are compared in Latin, and for very common surnames (Иванов, Георгиев…) the first name must match too. Best scores win; each video is used once.
- Status per row: `sure`, `likely`, `check`; people set `ok` or `wrong`. Existing rows are never changed by later runs, so the match isn't redone.
- Lightning talk slots matched to a recording become talks on import.
- Result: 156 matches (147 sure, 2 likely, 7 check); talks with video went from 201 to 357. Spot check of 25 sure rows: all correct. Known wrong: TarnovoConf 2017 "За стартъпите и погребенията" → "Yana Petrova - Za ideite i podkrepata" (status `check`).

**Gotchas:**
- Old playlists sometimes have several lightning talk videos ("Lightning talks 01/02 – BurgasConf 2022"); only one is linked to the edition's lightning slot.
- PHP 8.4+ wants the `escape` argument for `fgetcsv`/`fputcsv`; `escape: ''` gives standard CSV.

**Open:**
- **2024, 2025 and most of 2026 have no per-talk videos on the channel**, only full-day livestream recordings (one or more per edition, 7–8 hours). How to link them is to be decided.
- TarnovoConf 2017 and 2018 and RuseConf 2017 found almost nothing: no playlists, and the channel titles don't match.
- The `check` and `likely` rows need a person to look at them.

## 2026-11-16 · Full-day recordings and talk timestamps

**What:** `bin/match-videos` also links remaining talks into the editions' livestream recordings at their start time (new `start` column in `import/video-matches.csv`, sources `stream-chapter` and `stream-time`). `migrations/004_recordings.sql` adds `talks.video_start`, `talks.video_start_approx` and `events.recordings`. Embeds start at the time (`?start=N` in the player, `&t=Ns` on the YouTube link); edition pages show "▶ Запис на целия ден" (parts numbered) once finished; talk pages explain that the video is part of the day's recording and when the start is approximate.

**Why / how:**
- From 2024 the channel has only full-day livestreams (7–8 h). yt-dlp gives each stream's real start (`release_timestamp`) and, for some, YouTube chapters with exact talk starts (VarnaConf, RuseConf and PlovdivConf 2024).
- Chapters are matched by title like the videos (status `sure` ≥ 90, else `check`). Without chapters: start = programme time − stream start, choosing the stream part that covers it; status `approximate`. Streams under 10 minutes are ignored (tests, mislabelled clips).
- Accuracy of the approximation, measured on VarnaConf 2024 against its chapters: from about half a minute to 6 minutes late.
- Stream details are saved in `data/import/youtube/streams/<id>.json` (yt-dlp needs `--ignore-no-formats-error` for these, or it fails with "not available").
- Result: 99 more talks linked (21 exact, 75 approximate incl. lightning talks); talks with video: 456 of 491; 15 editions have recordings.

**Gotchas (tests):**
- Playwright's `toHaveScreenshot({ fullPage: true })` captured lazily loaded images as blank even after they had loaded; `page.screenshot()` didn't. Screenshot checks now use `expectLooks(page, name)` in `specs/helpers.js`: scroll through the page so lazy images start, decode them, then `page.screenshot()` + `toMatchSnapshot()`.
- The site has `scroll-behavior: smooth`, so scrolling back to the top in a test animated and the screenshot was taken mid-scroll (3 px off, flaky). Test scrolls use `behavior: 'instant'`.

**Open:**
- Talks still without video: 35 (TarnovoConf 2017/2018, RuseConf 2017 have nothing; some first talks started before the stream did, e.g. VarnaConf 2025).
- The editor review of approximate timestamps belongs in the admin (see decisions.md).

## 2026-11-16 · Step 6: talks library and speaker directory

**What:** `it-tour.bg/talks` (`src/Pages/TalksLibrary.php`, `templates/pages/talks.php`, `partials/talk-results.php`) and `it-tour.bg/speakers` (`src/Pages/SpeakersDirectory.php`, `templates/pages/speakers.php`, `partials/speaker-results.php`); queries `Talks::library()`, `Talks::counts()`, `Speakers::directory()`; `src/Pager.php` + `partials/pager.php`; `Text::searchWhere()`; `list_url()` helper; instant search in `site.js`.

**Why / how:**
- **Talks:** newest edition first, programme order within it; filters: search (title or speaker), city, year, "Само със запис". Rows as in the mockup: city dot and year, title, speakers · edition, "▶ запис" / "предстои".
- **Speakers:** published speakers with at least one public talk, alphabetical (decisions.md); filters: name, city and year (where and when they spoke). The card says "N лекции · cities" or, for one talk, the edition.
- **Search:** every word must match; each word matches as typed or transliterated against `search_text` (lowercase + Latin), so "наков" and "nakov" find the same. `LIKE` wildcards in the query are escaped. Searching Cyrillic for a Latin title ("докер") doesn't match; fine for now.
- **Pages:** 24 per page; links show 1, neighbours, last and "…" gaps (a one-page gap shows the page). `?page=` that is not a positive number, or past the end, is a 404; a search with no results is a 200 with "Няма лекции… Изчисти филтрите".
- **Instant search** (agreed with the user): the plain GET form works without JavaScript; with it, `site.js` fetches the same address with `X-Partial: results` and swaps `[data-results]`.
  - Typing waits 300 ms after the last key; an older request still running is aborted.
  - History: the first search of a typing run is pushed, the rest replace it (Back goes to before typing, not to half-typed words); a filter change or page link pushes; leaving the search box ends the typing run. Back/Forward refill the form from the address and load its results.
  - The history entry is written when the request starts, not when it ends, so an aborted request can't lose it.
  - Empty values are left out of the address (`/talks?q=go`, not `&city=&year=`); page 1 has no `?page`.
  - The count (or "no results") is copied into a `role="status"` element for screen readers; the results block is dimmed with `aria-busy` while loading. If a request fails, the browser just goes to the address.
- **Caching and indexing:** responses of pages with a partial carry `Vary: X-Partial`. In production, filtered or searched lists have `<meta name="robots" content="noindex, follow">`; the plain lists and their numbered pages are indexed, with a canonical to themselves (layout's new `$noindex`).

**Gotchas:**
- `section.tight` beats a plain class, so `.archive-list { padding-top: 0 }` from step 4 never worked; now `section.archive-list` / `section.list-page` (the archive screenshot changed).
- Speaker cards in a grid stretch to the row; `align-items: start` keeps names aligned when city lines wrap differently.
- `pkill -f` with a pattern that also matches the calling shell kills the shell (again); stop test servers by job instead.

**Open:**
- 8 speakers have their names in Latin script, so they sort before everyone else.
- Talk and speaker `search_text` is filled by `bin/import`; the admin will have to refresh it on save (talks: title and speaker names, so renaming a speaker touches their talks).

## 2026-11-16 · Search text stays a stored column

Discussed with the user: computing the Latin form during each search (no stored field, never stale) versus the stored `search_text` column. Kept the column: plain lookups, room for SQLite full-text search later, usable outside our PHP. The cost is that saving a talk or speaker must rebuild it, including the talks of a renamed speaker. **The admin's save code must do this, with a test** (decisions.md).

## 2026-11-16 · Step 7: it-tour.bg homepage

**What:** `src/Pages/Home.php`, `templates/pages/home.php`, `src/Season.php` (the season's stops, next stop, season state, pop-up data), `src/Map.php` (outline and projection), `partials/status.php` (season pop-up), `partials/sponsors.php` (shared with event pages), `Talks::recentVideos()`, `Sponsors::forSeason()`, `migrations/005_city_positions.sql` (`cities.lat`, `cities.lng`, set by `bin/import`). The placeholder home page is gone.

**Why / how:**
- Sections (version 1 of decisions.md section 3, minus news, team and "get involved"): hero with photo, wordmark, heading, tagline, numbers and the season's badges; stops (map + list); recent videos (setting `home_videos`); season sponsors; newsletter box (only when `newsletter_url` is set; a link to the external platform, no form).
- **Season state** (`Season::current()`): the next stop is the first one not finished; the season is `live` when that stop is on now, `off` when none is left. Hero, stops text, badges and the map follow it: upcoming → "Виж спирките"; live → "▶ На живо: BurgasConf 2026" to the event's `#live`; off → "Сезон 2026 завърши. Датите за 2027 предстоят." (plus "Абонирай се" when the newsletter is on).
- **Numbers are counted:** conferences already held (finished editions), first year, public talks, speakers with a public talk.
- **Map:** the design's outline in a 1000 × 680 box. Its city dots fit a straight-line projection exactly (x = 150·lng − 3329.5, y = 9058.6 − 204·lat), so `Map::point()` uses that with each city's latitude and longitude (decisions.md: map position is copied from Google Maps). The line joins the stops in date order: solid up to the next stop, dotted after. Labels go right of the dot, or left for cities east of x = 700 (Varna, Burgas). A city without a position is left off the map but stays in the list.
- **Videos:** one talk with a video and still from each of the four latest editions that have one (the first in the programme), so the block isn't four talks from one day.
- **Season pop-up** on every main-site page (`View::page` adds it; city sites show their own state): next stop with "След N дни" and a link to the programme (or the event), or "На живо сега" with what's on stage. Off-season there is none. Closing it keeps it closed for the visit (`sessionStorage`, keyed by event and state, so a live pop-up still appears after closing the "next stop" one).

**Gotchas:**
- The pop-up is `position: fixed`, so in full-page screenshots it lands mid-page; the test closes it first.
- The test for "conferences held" must use the faked date: on 20 July 2026 it's 59, not 62.

**Open:** see decisions.md, "Homepage, open points from step 7" (no season sponsors in the old data, short tagline, the dropped "0 лв." fact).
