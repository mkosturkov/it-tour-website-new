# IT Tour website: design mockups

Static HTML mockups for a new IT Tour website. IT Tour runs free, one-day IT conferences in five Bulgarian cities, one city per month in summer, since 2012.

Today there is a hub site (it-tour.bg) plus a separate site per city (plovdivconf.com, tarnovoconf.com, burgasconf.com, varnaconf.com, ruseconf.com), all on one old app. The plan is to replace them with one platform: one shared design, with each city keeping its own logo and colour.

**This repo is design only.** There is no backend, build step or framework. Every page is a plain HTML file you can open in a browser.

## See it

- **Online:** the GitHub Pages link for this repo (Settings → Pages).
- **Locally:** open `designs/index.html` in a browser, or run `python3 -m http.server` in the repo root and go to http://localhost:8000/designs/.

`designs/index.html` is the chooser page. It links to every version and every page.

## Where we are

1. **Requirements.** Agreed scope, page list and open questions are in [docs/decisions.md](docs/decisions.md). Read it before suggesting changes.
2. **Step 1: homepage in several directions.** Versions A–D. After feedback, C was dropped and the others were merged into version E.
3. **Step 2: full page set in version E.** Done. This is the current direction.
4. **Next:** team feedback on E, the open questions below, then the real logo and photos.

## The versions

| Folder | Name | Status |
| --- | --- | --- |
| `designs/a-tour` | A, The Tour | Homepage only. Kept for reference. |
| `designs/b-hackerspace` | B, Hackerspace | Homepage only. Kept for reference. |
| `designs/c-editorial` | C, Editorial | Homepage only. Dropped. |
| `designs/d-network` | D, Network | Homepage only. Kept for reference. |
| `designs/e-tour-dark` | **E, Tour Dark** | **Current direction. Full page set.** |

Older versions are kept on purpose. Don't delete them.

## Version E pages

- **Homepage** (`index.html`). What IT Tour is → this year's stops on a map → news → team → videos → get involved → sponsors → subscribe. The season status (next stop, or live now) slides in as a small pop-up after load.
- **Event pages.** One per city, each showing a different stage of an event:
  - `event-plovdiv.html`: early, call for speakers open, no programme yet
  - `event-varna.html`: before the event, with countdown, programme and an "I'm coming" button
  - `event-burgas.html`: on the day, live stream and "now on stage"
  - `event-ruse.html`: after the event, with videos, photos and a write-up. Every archived edition would look like this.
- **Archive** (`archive.html`) with city and year filters.
- **Talks** (`talks.html`, `talk.html`) and **speakers** (`speakers.html`, `speaker.html`).
- **Team** (`team.html`). Both layouts we are choosing between, behind a toggle.
- **News** (`news.html`, `news-article.html`).
- **Get involved:** `volunteer.html`, `speak.html`, `sponsors.html`.
- **Info:** `about.html`, `code-of-conduct.html`.

## The two review switches

These exist only to review the mockups. They won't be on the real site.

- **Season** (bottom of the page): upcoming / live / off-season. It changes the status pop-up. Event pages always show their own stage.
- **Accent colour** (pill in the top right): blue, green, violet, teal and white, in soft-to-vivid variants. The choice is remembered as you move between pages.

Both can also be set in the URL, for example `?season=live&accent=green2`.

## What is real and what is placeholder

- **Real:** city names, logos and colours; the 2026 dates; the BurgasConf 2026 and PlovdivConf 2025 talks shown; 2026 sponsors and partners; "62 editions since 2012".
- **Placeholder:** most other talks and speakers, all team members, the Varna programme, photos, form fields, and the IT Tour logo (a ring placeholder until we have the real one). Pages with sample content have a thin "Макет" note at the top.
- **Estimates:** "230+ speakers" and "350+ talks". We need the real counts.
- The mockups pretend it is mid-July 2026, with BurgasConf (25 July) as the next stop.

## Open questions for the team

The full list is at the end of [docs/decisions.md](docs/decisions.md). The main ones:

1. Team page: grouped by city, or one flat list?
2. Which accent colour for E?
3. Which ideas should we use to get people to register (optional "I'm coming")?
4. Who has the past event photos?
5. What are the real speaker and talk counts?

## How to suggest changes

Open an issue or a pull request. Small, focused PRs are easiest to review: one page or one idea each. If you use an AI coding agent, point it at [AGENTS.md](AGENTS.md) first. It explains the structure and the rules.

The content is in Bulgarian. These docs are in English.
