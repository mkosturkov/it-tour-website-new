# Guide for AI agents

You are helping the IT Tour team change static design mockups and open pull requests. Read this file and `docs/decisions.md` before editing anything. `README.md` has the overview.

## Ground rules

- **Work in `designs/e-tour-dark/` or `designs/f-brand/`.** These are the two current directions, with the same pages and structure. Change the one the person asked about; if a change should apply to both, make it in both. Do not edit or delete `designs/a-tour`, `b-hackerspace`, `c-editorial` or `d-network`. They are kept for reference.
- **No build step and no dependencies.** Plain HTML, CSS and vanilla JS only. No frameworks, npm packages, bundlers or CSS preprocessors. Google Fonts is the only external resource.
- **Paths are relative.** The site is served from a GitHub Pages subpath, so never use root-absolute links like `/designs/...`.
- **Page content is in Bulgarian.** Code, comments, commit messages and docs are in English.
- **Don't invent facts.** Use placeholders ("Име Фамилия", "Примерна лекция") and say so in the page's `.mock-note`. Real data is listed in `README.md`.
- **Decisions live in `docs/decisions.md`.** If your change settles or raises a question, update that file in the same PR. Use ✅ for decided, ❓ for open and 🔜 for later.
- **One idea per PR.** Describe what changed and why, and add before/after screenshots for visual changes (desktop around 1400px wide and phone at 390px).

## How versions E and F are built

F started as a copy of E. The HTML and `site.js` are almost the same; the look lives in `site.css`. F's stylesheet is E's with light tokens, plus a "brand layer" at the end. F's brand assets (logo, city badges, textures, photo) are in `f-brand/img/`.

```
designs/
  index.html          chooser page linking every version and page
  shared/mockup.js    season switch (upcoming / live / off), shared by all versions
  shared/mockup.css   styles for the season switch
  shared/logos/       city logos
  e-tour-dark/ and f-brand/
    site.css          ALL styles for that version: tokens, components, responsive rules
    site.js           injects header, footer and status pop-up; accent switch; small demos
    *.html            one file per page
```

### Page skeleton

Every E and F page follows this shape. Copy an existing page rather than starting from scratch.

```html
<head>
  ...
  <link rel="stylesheet" href="../shared/mockup.css">
  <link rel="stylesheet" href="site.css">
  <script src="../shared/mockup.js"></script>
  <script src="site.js"></script>
</head>
<body data-page="about">
<div data-slot="header"></div>
<div class="mock-note"><div class="wrap"><b>Макет:</b> what is placeholder here.</div></div>
<main>
  <section class="page-hero"><div class="wrap">...</div></section>
  <section>...</section>
</main>
<div data-slot="footer"></div>
<div data-slot="status"></div>
</body>
```

- `data-slot` divs are filled by `site.js`. Header, footer and pop-up exist only there, so change navigation or footer links in `site.js` (the `NAV` and `CITIES` lists), never in individual pages.
- `data-page` on `<body>` marks the active menu item. Values: `home`, `about`, `stops`, `news`, `talks`, `archive`, `team`, `involve`.
- Omit `<div data-slot="status">` on pages that already show the event state, such as the live Burgas page.
- Content goes inside `.wrap`. Sections alternate backgrounds with `class="alt"`.
- When you add a page, also link it from `designs/index.html` (in the E card) and from wherever it belongs in the site.

### Styling

- Use the tokens at the top of `site.css`. Don't hard-code colours.
  - Surfaces and text: `--bg`, `--bg-2`, `--panel`, `--panel-2`, `--line`, `--text`, `--muted`, `--dim`, `--live`.
  - Site accent: `--accent` and `--accent-ink` (text on accent). They change with the accent switch.
  - City colours: `--plovdiv`, `--tarnovo`, `--burgas`, `--varna`, `--ruse`. An event page sets `style="--c:var(--burgas)"` on `<body>`, so the city colour drives its hero while buttons keep the site accent.
- Fonts: in E, Space Grotesk for headings and Inter for text. In F, Montserrat throughout, plus `--display` for the wordmark.
- Reuse existing classes before adding new ones: `btn btn-primary`, `btn btn-ghost`, `btn-sm`, `page-hero`, `crumbs`, `prose`, `two-col`, `aside-card`, `form`, `pills`, `tabs`, `rowlist`, `schedule`/`slot`, `speakers`, `ev-hero`, `side-card` and others. Search `site.css` first.
- Add new CSS to `site.css` near related rules, and include the 960px and 560px responsive cases.
- Every page must work at 390px wide with no horizontal scroll.

### Behaviour in site.js

- Season: `<html data-season="upcoming|live|off">`, set by `shared/mockup.js`. Show something only in some seasons with `data-show="live"` or `data-show="upcoming off"`.
- Accent: `<html data-accent="...">`. Variants are defined in `site.css` under `[data-accent=...]` and listed in `OPTS` in `site.js`. The default is `blue2` in E and `navy` in F. In F, event pages always use their city colour.
- Tabs: a `[data-tabs]` wrapper with `[data-tab="x"]` buttons and `[data-panel="x"]` panels.
- RSVP demo: `[data-rsvp]`. Forms: submit is cancelled and `.form-ok` is shown. Nothing is sent anywhere.
- Both switches also read the URL: `?season=live&accent=green2`.

## Checking your work

1. Serve the repo root (`python3 -m http.server`) and open `http://localhost:8000/designs/e-tour-dark/`.
2. Check the page at desktop and phone widths, in all three seasons, and with at least two accent colours. Make sure buttons stay readable on `white2` and `violet3` in E, and on every city colour in F.
3. Click every link you added or changed.
4. Optional headless screenshot:
   `google-chrome --headless=new --hide-scrollbars --window-size=390,2400 --screenshot=out.png "http://localhost:8000/designs/e-tour-dark/about.html?season=off"`

Don't commit screenshots to the repo. Attach them to the PR.
