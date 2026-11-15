# IT Tour website: decisions and open questions

Status: version F chosen. Planning the real site: features, content and technology decided step by step (see the last sections).
Last updated: 2026-11-15

Legend: ✅ decided · ❓ open, to discuss · 🔜 later · 📝 note for later work

---

## Background

- The current hub is **it-tour.bg**. Each city has its own domain: plovdivconf.com, tarnovoconf.com, burgasconf.com, varnaconf.com and ruseconf.com. blagoevgradconf.com has been inactive since 2014.
- All of them run on one legacy app. Past editions live at `<city>conf.com/archive/<year>`.
- The events are free, one day, single track, roughly 10:30 to 18:00 plus an after party. Content is in Bulgarian.
- Main problems with today's sites:
  - Between seasons the hub looks dead.
  - The hub never explains what IT Tour is.
  - The archive is a flat list of 62 logos.
  - Talks, videos and speakers can't be browsed.
  - There are no pages for speakers, sponsors, volunteers or the team.
  - Some social links are broken, analytics stopped collecting in 2023, and pinch-zoom is blocked on mobile.

---

## 1. Scope

- ✅ One new platform replaces both the hub and all the city sites. Everything shares one design, and each city has its own accent colour.
- ✅ Old archive links keep working.
- ✅ Active cities: Plovdiv, Veliko Tarnovo, Burgas, Varna, Ruse.
- ✅ Blagoevgrad appears only in the archive.

## 2. Language

- ✅ Launch in Bulgarian only.
- ✅ Build so English can be added later. The mockups show a BG/EN switch that does nothing yet.

## 3. Homepage (it-tour.bg)

- ✅ From top to bottom:
  1. **Season banner.** During the season it shows the next event and a countdown. Between seasons it says the next year's dates are coming and asks people to subscribe. While an event is live, it shows the live stream.
  2. **Latest news** (three items).
  3. **This year's tour.** The five cities with dates and venues, on a map or timeline.
  4. **What IT Tour is.** Free, one day, travelling, community-run since 2012.
  5. **Numbers.** Years, editions, talks, speakers. ✅ The site counts these from its own content when it is built, so nobody updates them by hand.
  6. **Recent talk videos.**
  7. **This season's sponsors.**
  8. **Get involved.** Three cards: speaker, sponsor, volunteer.
  9. **Email signup**, then the footer.

## 4. Event page (one per city)

- ✅ Sections: date, venue and free entry · schedule · speakers · venue map · after party · sponsors · local team · this city's past editions.
- ✅ The page changes with the event's stage:
  - **Before:** date and venue, call for speakers. Later, the schedule and speakers.
  - **During:** a live stream section at the top. It has an embedded player, a YouTube link, and a link for external players, plus a "now on stage" marker.
  - **After:** videos for each talk, photos, a write-up link, and "see you next year".
- ✅ Which city shows which stage in the mockups:
  - Plovdiv: before, call for speakers open, no schedule yet
  - Varna: before, schedule published
  - Burgas: live
  - Ruse: after

## 5. Archive, talks and speakers

- ✅ **Archive:** filter by city and year. Each past edition is the "after" event page, kept permanently.
- ✅ **Talks library:** every talk from every year. Search plus city and year filters. Video and slides where available.
- ✅ **Speaker directory:** a profile for each speaker with photo, bio, links, and all their talks across cities and years.
- ✅ Every talk shows a short summary: under its title in the programme, on the speaker profile and on the talk page. The text comes from the "Описание" field of the speaker form. The mockups use placeholder summaries.
- ✅ No topic tags for now.
- 🔜 Topic tags (AI, web, security…) could be added later.

## 6. Info and involvement pages

- ✅ **Become a volunteer:** its own page with a placeholder form. It covers both the whole tour and local events.
  - ✅ **Form fields:**
    - Name (required)
    - Email (required)
    - Phone (optional)
    - Link to a social media profile (optional)
    - **Which stops?** Multi-select of the five cities. Selecting all of them means the whole tour.
    - **How can you help?** Checkboxes for registration desk, video and streaming, social media, logistics and other, plus a free-text box.
    - Consent to store the data (required)
- ✅ **Team:** its own page, because the team may grow large.
  - ✅ **Layout:** one flat list (layout B). The team is small.
  - ~~City pages also show their local team.~~ Changed (November 2026): event pages don't show a local team. The team appears only on the team page, so everyone is treated as equal.
- ✅ **News:** the latest items on the homepage plus a news page. It covers short announcements and longer write-ups after events, with an optional city tag so city news also shows on that city's page.
- ✅ **Become a speaker:** explanation plus a placeholder application form.
- ✅ **For sponsors:** why sponsor, audience numbers, what sponsors get, a contact button. No price list.
  - ❓ No public prices for the first version. To confirm with the team.
- ✅ **About:** the IT Tour story since 2012.
- ~~Code of conduct.~~ Dropped for now (November 2026). The old sites never had one. Whether we want one is a question for the team.

## 7. Attendance and registration

- 🔜 **Optional registration: parked (November 2026), not in the first version.** An "I'm coming" button with name and email. Entry stays free and nobody is turned away; registration gives us a headcount and lets us send reminders.
- 🔜 **"Add to calendar" button: next version** (moved November 2026), to be rethought together with the calendar feed. The planned placement: each event page, and next to each stop in the homepage's stops list, available to everyone.
- ❓ **After the first version, to discuss with the team:**
  - A reminder the day before, plus the stream link if they can't make it.
  - A raffle among registered people, with prizes from sponsors.
  - Slides and videos emailed to them after the event.
- ❓ **To discuss with the team: registration and the "N people are coming" counter, both parked.** Why would anyone give a name and email just so a counter works? We need a clear reason before building it. If it is built, the default design was: name, email and consent; one count per email per event; organisers can export the list; data deleted 30 days after the event.
- ✅ **Not doing:** a badge printed in advance with faster entry, and priority for extras such as after-party spots, workshops or swag.

## 8. Branding and photos

- ✅ **City events keep their own logos and colours**, and every event page clearly shows it is part of IT Tour, for example with an "IT Tour" mark and links to the other cities.
- ✅ **Photos:** past event photos probably exist, but nobody has access right now. The mockups use placeholder images.
- 🔜 **Photo section, parked until we know whether we have access to the photos.** The planned design stays as in the mockups: about six highlights on each after-event page, plus a link to the full album hosted elsewhere (Google Photos, Flickr…). There is no gallery on our site. The first version launches without it.
- ❓ To discuss with the team: who has the past photos, whether we can use them, and whether to hire a photographer for 2027.

## 9. Design directions (four versions)

- ✅ Four directions for the mockups:
  - A, "The Tour": road-trip or concert-poster style, a map of Bulgaria with the route, ticket-style date cards.
  - B, "Hackerspace": dark, monospace accents, the schedule styled like a terminal.
  - C, "Editorial": light, large type, lots of whitespace, photo-led.
  - D, "Network": a blend based on feedback.
- Feedback on the first three:
  - Tour: the travelling idea is liked, but it shouldn't feel like a concert. It must stay IT.
  - Hackerspace: dark plus monospace feels too obvious, but a technical touch is welcome.
  - Editorial: light and large headlines are liked, but it must not read like a newspaper.
- What D does with that feedback:
  - Light and spacious, with large headlines (from C), but laid out on a grid of cards and panels rather than newspaper columns.
  - The tour shown as a network diagram: cities are nodes on a simplified outline of Bulgaria, connected in date order (from A, in IT language).
  - Small technical details only: monospace labels for dates, times and stats, and a schedule that reads like a clean timeline (from B, kept subtle).
  - The city colours light up their node and accents.

## 10. Deliverables

- ✅ Static HTML/CSS mockups in this repo, one folder per direction, using real Bulgarian content from the current sites and placeholder photos.
- ✅ **Step 1:** the homepage only, in all four directions (A, B, C, D). Desktop and phone.
- ✅ **Step 2:** once a direction is chosen, the full set of pages in that direction only:
  - event pages in the four stages: Plovdiv before with call for speakers, Varna before with schedule, Burgas live, Ruse after
  - archive, talks library, speaker profile
  - team (both layouts), news list and article
  - volunteer, speaker application, sponsors, about, code of conduct

---

## Questions for the team call

1. ~~Team page: grouped or flat list?~~ Decided: flat list.
2. Registration: is it worth having at all, with the "N people are coming" counter? It is parked for now. If yes, which incentives: the reminder, the raffle, emailing slides and videos (section 7)? The "add to calendar" button moves to the next version.
3. Photos: who has the past event photos, and can we use them? Until we know, the photo section is parked (section 8).
4. ~~Volunteer form: which fields do we need?~~ Decided (section 6).
5. Sponsors: are we sure about no public prices? The first version has none (section 6).
6. ~~Numbers: what are the real speaker and talk counts?~~ Decided: the site counts them from its content. A scrape of the old sites (all 62 archive pages, November 2026) found 438 talks and 221 speakers.
7. ~~Accent colour for version E.~~ No longer needed: version F was chosen.
8. ~~E or F?~~ Decided: F.
9. The "Back In The USSR DL" font came without a licence file. Can we use it on a public website? We use it in the meantime and switch to a fallback only if the answer is no.


## Version F (brand visuals)
- ✅ Built as `designs/f-brand`. It has every page and switch from E, styled after the IT Tour 2026 social media visuals.
- ✅ The real IT Tour logo and the city badges come from the Figma exports.
- ✅ Each conference page uses its city colour. The VarnaConf visuals are mint (#6ec3aa).
- ✅ City colours updated to the badge values: Plovdiv #c8cc05, Tarnovo #f38c16, Burgas #f18a85, Varna #6ec3aa, Ruse #019295.
- ✅ **F was chosen** (November 2026). E stays in the repo for reference.
- ❓ **Accent colour for F's hub pages** (homepage, archive, talks…). Still open. The accent switch stays in the mockups for now. Event pages always use their city's colour.
- ✅ Wordmark font "Back In The USSR DL" added. It has Latin letters only, so it is used for the Latin wordmarks.
- ✅ The real site uses this font. We assume we are allowed to, and switch to a fallback font only if it turns out we are not.
- ❓ The font came without a licence file. Ask the team to confirm it can be used on a website.

## Homepage feedback (round 1)
- ✅ C (Editorial) dropped. A: clean and easy to follow, but no paper or road-trip look. B: colours liked, terminal style too generic. D: section order confusing.
- ✅ Homepage order: what IT Tour is → stops (map with dates + city cards) → news → team → videos → get involved → sponsors → subscribe.
- ✅ Season status is a small pop-up that slides in after load (next stop / live now). No pop-up off-season.
- ✅ Dark theme, a bit lighter than B, not terminal-styled. Built as version E (designs/e-tour-dark).

## The real site (planning started November 2026)
- ✅ **Version 1 is everything we have enough information to build.** Anything that still needs decisions moves to the next version.
  - **In version 1:**
    - homepage and event pages
    - archive page with filters
    - talks library and talk pages
    - speaker directory and profiles
    - team page (the "about" texts stay empty until we have the bios)
    - sponsor lists on the homepage and event pages
  - **Next version:**
    - volunteer form (where it goes and how it works)
    - news
    - about page (with general text pages)
    - the "become a sponsor" pitch page
    - the homepage's "get involved" section and the Включи се menu item
    - "add to calendar", the calendar feed and RSS
  - **Knock-on in version 1:** the menu hides Новини, За IT Tour and Включи се, and the homepage hides the news block and the "get involved" section.
- ✅ Editors change these settings themselves, without a developer: the hub accent colour, and each city's colour and logo.
- ✅ Editors can change the homepage tagline.
- ✅ **Season state is automatic, from the event dates and times** (Bulgarian time):
  - **Live:** between an event's start and end time, roughly 10:30 to 18:00. The after party doesn't count. The live stream shows only then.
  - **Upcoming:** from the start of the season until the last stop.
  - **Off-season:** from the day after the last stop until the next season's first date is published. The homepage then says the next dates are coming and asks people to subscribe.
  - There is no manual switch. If an event runs late, the editor changes its end time. If the stream is cancelled, the editor removes the stream link.
- ✅ **Newsletter:** we don't run our own mailing list. The signup form sends people to an external newsletter platform, which also handles unsubscribing as EU rules require. The platform is chosen with the rest of the technology. Editors can turn the newsletter off, which hides every signup form.
- ❓ To ask the team: does anyone send emails to the old sites' subscriber list today, and should that list move to the new platform? The list exists only in the old app's database.
- ✅ **News can be turned off as a whole.** That hides the news page, the menu link and the homepage block. The homepage block can also be turned off on its own, for example while there are only one or two items.
- ✅ **The homepage videos block can be turned off.**
- ✅ **No team on the homepage.** The team appears only on the team page. Showing a few people on the homepage would put them above the others, and everyone on the team is equal. This replaces "team" in the homepage order agreed in round 1.
- ✅ **Sponsors are set by hand by editors:** one set for the homepage each season, and a separate set for each event page. One shared list of sponsors (with logo, link and type, such as media partner) feeds both.
- ✅ **Sponsors in detail:**
  - A **sponsor record** is set once and reused: name, logo, website link and an optional "about".
  - A **sponsorship** links a sponsor to an event, or to the homepage for a season. It has a type, written as free text (for example "Златен спонсор", "Кафе", "After party", "Медиен партньор"), and an order.
  - The type and order belong to the sponsorship, not to the sponsor, because the same company can sponsor different things in different cities and years.
  - Pages group sponsors under their type, in the order editors set.
- ✅ **"Get involved" on the homepage:** three cards (speaker, sponsor, volunteer) linking to their forms. Editors can change the text and turn each card off.
- ✅ **City settings, set once:** name in Bulgarian and in Latin letters, position on the map, badge, and old domain. The badge is an SVG that the site recolours to each year's colour. When a city gets a new badge design, it is added with a starting year, and older editions keep the badge they had.
- ✅ **Event settings, set each year:**
  - the full name, typed in (for example "VarnaConf 2026") rather than generated
  - colour, which also colours the badge
  - date and start and end times
  - venue and after party, each with an address and a map link
  - live stream link
  - call for speakers deadline
  - whether "I'm coming" is on
  - Facebook event and YouTube playlist links
  - intro text
- ✅ **Adding or removing a city:** the map shows only cities that have an event this season. To leave a city out, don't create an event for it that year; its old editions stay in the archive. To add a city, create the city with its map position and badge, then its event.
- ✅ **City domains stay in use for now.** Each city's event pages are served on its own domain (varnaconf.com and so on) rather than redirected to it-tour.bg. This replaces the earlier default of redirecting everything.
- ❓ Other kinds of events, beyond the yearly city conferences, may come later. The event settings can take an "event type" field when needed.
- ✅ **Map position** is given as latitude and longitude, copied from Google Maps (right-click on the place). The site turns it into a dot on the map of Bulgaria.
- ✅ **A season is a calendar year.** Events belong to a season through their date. A small season record holds only that year's homepage sponsors.
- ✅ **Call for speakers:** each event has its own deadline date, separate from the event's date and times. The section shows automatically until that date, with a button to the speaker form with the city already selected. No deadline means no call for speakers.
- ✅ **Programme:**
  - The programme is a list of time slots. Each slot has a required start time and an optional end time. Without an end time, a slot ends when the next one starts.
  - A slot is either a talk or something else. "Something else" is a free-text title (coffee, opening, lunch) with optional short text.
  - Anything with speakers is a talk, including panels and lightning talks.
  - A talk is its own record: title, summary, one or more speakers, and video and slides links. The same record appears in the programme, the talks library and the speaker profiles.
  - The programme stays hidden until the editor publishes it.
  - "Now on stage" is worked out from the slot times while the event is live.
- ✅ **Speakers:**
  - One record per person, reused across years: name, bio, photo, job title, and links such as a website, LinkedIn or GitHub.
  - Every page shows the current bio and photo, whatever the year of the talk. This matches common practice, lets speakers update their details once, and removes an old photo everywhere when asked. There are no per-year versions on the public site; whether to keep a change history internally can be decided later.
  - The event page's speakers block is filled from that event's talks.
  - Private contact details (email, phone) are not part of the public record.
- 📝 **Note for the import of the old sites:**
  - The old system already reuses one speaker record across years: 85 speakers appear in two or more editions.
  - It keeps only one bio and photo per speaker and shows them for every year, which matches the new site.
  - Three names appear under two records each: Иван Димитров (23, 104), Тодор Тодоров (20, 172) and Николай Маринов (114, 115). These may be duplicates or different people.
  - The import should flag possible duplicates for a person to review, also checking for spelling variants, and never merge them automatically.
- ✅ **Venue and after party:** each has a name, an address, optional "how to get there" text and an embedded map. The editor copies the embed link from Google Maps (Share → Embed a map). It needs no API key.
- ✅ **The after party is optional and has a visibility switch.** It can be fully set up but hidden from the public until the editor shows it.
- ✅ **The after party is its own section on the event page and appears nowhere else.** It is not a slot in the schedule and not part of the venue details. (The Burgas and Varna mockups show it in both places; the real site won't.)
- ✅ **The after party also has a start time** (for example 20:00), shown in its section. **While hidden, the section disappears completely,** with no "coming soon" text.
- ✅ **Live block,** shown only while the event is live:
  - the embedded YouTube player and a "watch on YouTube" button, from the live link the editor pastes
  - the link for external players (a direct stream address, for apps like VLC), shown when the editor fills it in
  - "now on stage", from the programme times
- ✅ **Programme changes on the day must be quick and easy.** If a speaker doesn't arrive, an editor adds a replacement speaker and talk and changes the slot on the spot, from a phone. (Talks and speakers as entities: decided below.)
- ✅ **After the event:**
  - Each talk shows its video once the editor adds the link, and the event page has a button to the YouTube playlist.
  - The recap is an optional link to a news article, or optional short text on the event page when news is turned off.
  - Photos are parked.
  - The event page itself becomes the archive page. There is no separate copy.
- ✅ **Event page, last blocks:** "other stops this year" and "this city's past years" are filled in automatically. Event sponsors are set by hand, with details still to discuss.
- ✅ **Archive:**
  - A separate page on it-tour.bg lists every edition, grouped by year (newest first) and filtered by city and year. It replaces the old "Архив" section on the homepage.
  - Each card shows the city badge in that year's colour, name, date, venue, and the number of talks and videos, all automatically.
  - Every event page also lists that city's past years, as the old city sites do.
- ✅ **Old URLs stay the same wherever possible.** We can add new URLs, but we change an old one only when it is absolutely necessary. Examples: `<city>conf.com/` for the current edition, `<city>conf.com/archive/<year>` for each edition, and `<city>conf.com/speakers/<id>`.
- 📝 Before building, make a full list of the old sites' URL patterns. The scrape is a good moment to do it.
- ✅ **Talk fields:**
  - title
  - summary of two or three sentences, for the programme and the library
  - optional full description, for the talk page
  - one or more speakers
  - the event it belongs to
  - kind: talk, panel or lightning talk
  - optional video and slides links

  Its time comes from the programme slot.
- ✅ **Talks library** (on it-tour.bg): search by title or speaker, city and year filters, a "has video" filter, newest first.
- ✅ **Talk page:** title, edition, description, embedded video when there is one, slides link, speakers with links to their profiles, and more talks from the same edition. Talk pages are new, so their addresses are free to choose.
- ✅ **Speaker directory** (on it-tour.bg): search by name, city and year filters, alphabetical, each with a photo, name and number of talks.
- ✅ **Speaker profile:** photo, bio, job title and links, then all their talks across cities and years. The old addresses `<city>conf.com/speakers/<id>` keep working on every city domain, with the old numbers kept.
- ❓ For the technology discussion: how fast must an edit go live? A static site typically takes one to two minutes to rebuild, while PHP or WordPress shows changes immediately. This matters for programme changes on the day.
- ✅ **News:**
  - An article has a title, date, cover image, short intro, body text and an optional city tag.
  - The news list is newest first and can be filtered by city.
  - A city-tagged article also appears on that city's event page.
  - The whole feature can be turned off (see above).
- ✅ **Speaker applications:** for now the site only links to a speaker application page, which stays empty. Applications will probably go through an external system or a separate module of the site.
- ❓ The whole call-for-speakers feature (form, review, external system or own module) is to be discussed later. One idea: a styled form on our site that sends answers to Google Forms. The official Google Forms API cannot submit answers. Posting directly to a form's undocumented address works, but it can break without warning and can't confirm that the answer arrived.
- ✅ **Sponsor contact:** for now the contact button links to an empty placeholder page. The sponsors page itself (why sponsor, audience, what sponsors get) stays.
- ❓ To discuss with the team: how sponsors contact us. It could be a form, or simply an email address.
- ✅ **No code of conduct page for now.** The mockup page stays in the repo for reference.
- ❓ To ask the team: do we want a code of conduct? Some speakers and sponsors look for one.
- ✅ **Team members:** one record per person with name, photo, role and a longer "about" text. There is no city field; if it matters, the role can mention it ("organiser, Varna"), because people may belong to things other than a city. Editors add, remove and reorder people.
- ❓ **On hold: general text pages.** Instead of separate about (and code of conduct) pages, editors could create any number of plain pages with a WYSIWYG or Markdown editor and link them from the menu.
- ✅ **Main menu, fixed** (editors can't change it in the first version): За IT Tour, Спирки, Новини, Лекции, Лектори, Архив, Екип, Включи се, and an "Абонирай се" button. Новини and Абонирай се hide when news or the newsletter is turned off. Event pages also get a second bar linking to their own sections.
- 🔜 An editable menu can come with the general text pages (on hold).
- ✅ **The BG/EN switch is hidden** until English exists.
- ✅ **Footer:**
  - the five cities, linking to their domains
  - the menu pages
  - get-involved links
  - social links, as a list editors can change (the old site's Facebook and Twitter links are broken)
  - the contact email info@it-tour.bg
- ❓ **To discuss with the team: analytics.** No analytics in the first version for now. The old sites still load Universal Analytics, which stopped working in 2023. If we add analytics, the default suggestion is a cookie-free tool. It needs no cookie banner and gives reliable page views and referrers, but only rough unique visitors and no returning visitors.
- ❓ **Forms, to figure out later:** speaker application, sponsor contact and volunteer form. What happens after someone sends one is undecided: an email to the organisers, storage, an external tool, or our own module. The volunteer form's fields are decided.
- 📝 Option with no server: JavaScript builds a `mailto:` link from the form fields, with the subject and body filled in, and opens the visitor's email app ready to send. Limits:
  - It needs an email app set up on the device; webmail users on a computer often have none.
  - Plain text only, no attachments, and very long bodies can be cut.
  - We can't tell whether it was actually sent.
  - A fallback could show the text with a "copy" button and the address.

## Content from the old sites
- ✅ **Everything public on the old sites is brought over** by scraping them.
- ✅ **Old editions keep their original logo and colour.** Each old edition page has its own colour (for example Varna was #3c3b91 in 2012 and #6A9BCE in 2025) and its own logo image. On the new site an event may have its own logo image, which is then shown instead of the recoloured city badge.
- ✅ **Missing video links are matched during the import.** 40 of 62 editions have no video links, including every edition since 2022. A script compares the YouTube channel's video titles with the talk titles, links clear matches, and lists unclear ones for a person to check. The details get revisited when we plan the import.
- 📝 Already noted above: speaker duplicates are reviewed by a person; the subscriber list is a question for the team.
- 📝 **Full-size images are available.** The old site shows small thumbnails, but the originals are still on the server: drop the `thumb_` prefix from the file name. Examples: a speaker photo is 1000×1000 instead of 80×80, and an event logo is 2008×2008 instead of 88×88. The import takes the originals.

## Technology
- ✅ **We build our own site in plain PHP with an SQLite database,** with no ready-made CMS, so it stays as light as possible.
  - Pages are rendered by PHP on each request.
  - It runs on cheap PHP hosting: copy the files and the database file.
  - Rejected: WordPress (needs active updates and is a common target), Drupal (more than we need), ProcessWire and Kirby (ready-made, but heavier than we want), and Pages CMS with a static generator (depends on a small third-party service).
- 🔜 **A page cache can come later, only if needed.** Rendered pages would be saved as files, the server would deliver them directly, and they would be deleted on save and re-rendered on the next visit. Not in the first version: caching tends to bring more problems than expected.
- ✅ **Redirects:** requests that match no page go to PHP, which checks a redirects table editors can manage, then shows a 404 page.
- ✅ **No framework and no Symfony components.** The router (about 15 URL patterns, plus picking the city from the domain) and the maintenance command-line scripts are our own small code.
- ✅ **Templates are plain PHP files,** with our own small `render()` function, layouts and partials, and an `e()` escaping helper. A check script fails if any `<?= $...` output isn't wrapped in `e()`. It runs before every deploy. Rejected: Twig (a whole engine just for escaping), League Plates (quiet since 2024) and Laminas View (brings too much of Laminas).
- ✅ **Libraries only where mistakes are dangerous.** They are installed with Composer, and the `vendor` folder is committed so deploying stays "copy the files". Built-in PHP covers the rest: PDO for SQLite, password hashing, sessions, and GD for images.
- ❓ Formatted text for editors (Markdown or a WYSIWYG editor, and which library): decided later.
- ✅ **Domains mirror today's structure.** No old address is redirected. New pages go where they fit.
  - **City domains** keep exactly what they have today:
    - `/` shows the current or latest edition
    - `/archive/<year>` shows each edition, including the current year
    - `/speakers/<id>` shows the speaker profile
    - blagoevgradconf.com shows its last edition (2014)
  - **it-tour.bg** gets everything that spans cities, which is all the new pages: homepage, `/about`, `/team`, `/news`, `/archive`, `/talks` and talk pages, `/speakers` and profiles, `/volunteer`, `/sponsors`, `/speak`.
  - The main menu is the same on every domain and links across domains.
- ✅ **Speaker profiles exist on every domain, with it-tour.bg as the canonical address.** The city copies say so in a canonical tag and are not redirected, so visitors stay on the city site and its look. Today the same profile already answers on every domain (for example `varnaconf.com/speakers/127`) and no old page has a canonical tag, so this reduces existing duplication rather than adding any.
- ✅ **We plan for plain hosting with nothing in front of it,** so no Cloudflare or other CDN. If we get Cloudflare later, it is a bonus, not a requirement.
- ✅ **The admin lives on its own subdomain, `admin.it-tour.bg`.** That keeps its login cookies and rules separate from the public site. The name doesn't need to be hidden, because the protection comes from the login, not the address.
- ❓ **Admin login, default to revisit:** strong passwords with PHP's built-in hashing, two-factor login with an authenticator app, rate limiting after a few wrong tries, and no shared second password on the subdomain.

- ✅ **Server configuration is kept minimal.** We ship an `.htaccess` for Apache and LiteSpeed (most cheap hosts) and an nginx configuration example in the deployment notes, because nginx ignores `.htaccess`. Only these need the server: routing every address that isn't a real file to `index.php`, compression, long caching for files, HTTPS and the `www` redirect. Everything else (redirects table, 404 page) lives in PHP.
- ✅ **Compression is done by the web server only,** with no PHP fallback, because PHP can't compress static files. Deployment checks that it works.

### Front end
- ✅ **Machine-readable:**
  - Content is in the HTML from the server, with semantic elements.
  - schema.org structured data is added as JSON-LD (Event, Person, Organization, VideoObject, BreadcrumbList), and must match what the page shows.
  - JSON-LD rather than microdata (`itemprop`/`itemscope`): it is what Google recommends, it is easier to parse without walking the HTML, and it keeps the HTML minimal.
- ✅ **Accessibility: WCAG 2.2 level AA.**
  - It covers what the EU rules require. The European Accessibility Act mostly covers commercial services, and the Web Accessibility Directive covers the public sector, so we are probably not legally bound. Both point to WCAG.
  - It is also the global benchmark.
  - In practice: readable contrast (the city colours need checking), full keyboard use, labels for screen readers and alt text, nothing that relies on colour alone, and text that scales.
- ✅ **Semantic, minimal HTML.** Whatever CSS can do goes in CSS. Extra elements only where the design or accessibility needs them.
- ✅ **Server-rendered pages with a little JavaScript.** No single-page app and no framework such as Vue. Every page works without JavaScript; it only adds filters, the countdown, the mobile menu and tabs (progressive enhancement).
- ✅ **`llms.txt`** at it-tour.bg's root: a Markdown guide to the site for AI tools (summary, this season's events and dates, links to the archive, talks, speakers and get involved). PHP renders it from the database on each request like any other page, with no AI involved. No major search engine or AI company has confirmed using it, but it costs little.
- ✅ **`robots.txt` and `sitemap.xml` on every domain,** generated automatically:
  - Public domains allow all crawlers, AI crawlers included, and point to their own sitemap. The old sites have no sitemap.
  - The admin subdomain disallows everything and also sends a "don't index" header on every response.
- ✅ **Meta tags on every page:**
  - title, description, canonical and `lang="bg"`
  - Open Graph: title, description, URL, site name, `bg_BG` locale, type (`website`, `article` for news, `profile` for speakers), and a 1200×630 image with alt text
  - `twitter:card` set to `summary_large_image`
  - `article:published_time` on news articles
  
  Open Graph covers Facebook, LinkedIn, Viber, Messenger, Slack, Discord and Telegram.
  
  Preview image:
  - event pages: an image the editor uploads, or else the default
  - news articles: the cover image
  - speakers: their photo
  - everything else: the site default
- 🔜 Automatically generated preview images for events (badge, name, date, city colour), with the images topic.
- ✅ **Page weight:**
  - under about 500 KB per page on a first visit, images included
  - one CSS file and one small JavaScript file, loaded with `defer` so it never blocks the page
  - fonts hosted on our own server (not loaded from Google Fonts, also for visitors' privacy), limited to the Latin and Cyrillic characters we use
  - YouTube and map embeds load only on click, behind a preview picture, because a single YouTube embed is about 1 MB
- 📝 **Reminder for the admin panel discussion:** a tool for generating visuals for social networks. Not discussed yet.

- ✅ **Further quality defaults:**
  - **Speed:**
    - the server compresses pages and files
    - CSS, JS, fonts and images are cached by browsers for a year
    - **Our CSS and JS:** a PHP helper adds the file's modification time to the address (`site.css?v=1731600000`). It changes whenever the file changes, with no build step.
    - **Uploaded images:** each file is named from a hash of its contents, plus a readable part and the size (`svetlin-nakov-3f9a1c2b-800.webp`). Files are written once and never overwritten, so they can be cached forever; replacing a photo makes a new file and the old one stays (undo is possible).
    - **Media library from the start:** every upload is a row in a `media` table (file, alt text, crop). Everything refers to media by number, including images inside articles (`media:123`, not a file path); saving an article records which media it uses. Deleting unused files is then one query.
  - **Security:**
    - **HTTPS everywhere,** set up on the web server and recorded in the deployment notes:
      - free Let's Encrypt certificates, renewed automatically (the host's "AutoSSL" or `certbot`), for all domains including the admin subdomain and blagoevgradconf.com
      - every `http://` address permanently redirected to `https://`
    - **No `www`:** every domain uses the form without `www`, and `www.` is permanently redirected to it. Today both forms work and neither redirects. All of roughly 2,900 links in the 73 old pages we scanned use the form without `www`; the footer only shows the text "www.it-tour.bg", and even that links to `http://it-tour.bg`.
    - **Security headers:**
      - `Content-Security-Policy`: only our own scripts and styles, plus YouTube and Google Maps for the click-to-load embeds. It includes `frame-ancestors`, so other sites can't frame our pages.
      - `X-Content-Type-Options: nosniff`
      - `Referrer-Policy: strict-origin-when-cross-origin`
      - `Permissions-Policy`, switching off camera, microphone and location. "Directions" links open Google Maps, which asks for location itself.
      - The admin gets the same headers, set stricter, plus "don't index" and "never cache".
  - **Accessibility:**
    - animations such as the pop-up's slide-in are turned off for people who ask for reduced motion
    - English text (talk titles, Latin wordmarks) is marked as English for screen readers
    - **Reduced motion:** for people whose system asks for less motion (`prefers-reduced-motion`), the status pop-up appears without sliding in, and smooth scrolling and animations are off.
    - **Language marks:** English text is marked (`lang="en"`) so screen readers switch pronunciation. Talk titles get a language field in the admin. Fixed Latin text such as the wordmarks is marked in the templates.
  - **Basics:**
    - **Favicons per site:** the IT Tour logo on it-tour.bg, and each city's badge in that year's colour on its domain, generated from the badge files.
    - **`apple-touch-icon`** (180×180 PNG) on every site, for "Add to Home Screen" on iPhones. No web app manifest.
    - **404 page:**
      - a real 404 status code
      - the site's usual header, menu and footer, in the city's colours on city domains
      - a short friendly message in Bulgarian
      - links to this season's events, the archive, talks and speakers
      - no search box
    - **Missed addresses are logged,** and the admin lists the most-hit ones so an editor can add redirects. Most useful in the first months after launch, to catch old links we didn't know about.
    - no cookies on the public site, so no cookie banner

- 🔜 **Cleanup of unused media files later, if ever needed.** Rough estimate: a few hundred MB over ten years, which cheap hosting easily holds.

- 🔜 **HSTS later.** The header `Strict-Transport-Security` makes browsers use HTTPS only for a domain, which closes the gap a plain redirect leaves on the first request (SSL stripping). The risk is that if a certificate breaks, visitors can't get in at all. Plan: enable it per domain once HTTPS and renewals work, start with a short time (one day) and raise it to a year. No "preload".

- ❓ **Content language, to think through later.** Some talks, summaries and speaker bios are in English rather than Bulgarian. Options: translate everything into the page's language, or allow a language to be set on every piece of content. Mixed titles also need a solution, such as "Vibe coding: новият модел на програмиране", where only part is English.

- 🔜 **RSS feed, next version.** Default idea: news articles plus automatic event updates written by the site (new event announced, call for speakers open, programme published, videos published), with one full feed on it-tour.bg and one per city domain. To design in both back-end and front-end terms.

- 🔜 **Calendar feed, next version.** One address (`it-tour.bg/calendar.ics`) that people subscribe to in Google Calendar, Apple Calendar or Outlook. Their calendar then checks it by itself, so new events and changes such as a new date or venue appear automatically. It uses the same event data and `.ics` format as the "add to calendar" button, which moves to the next version too and is rethought together with it.

### Images
- ✅ **Formats:**
  - SVG for logos and badges: smaller than PNG for this kind of drawing (the Varna badge is about 5 KB compressed) and sharp at any size.
  - WebP for photos.
  - A generated PNG wherever SVG isn't accepted, such as social previews.
  - The uploaded original is kept on the server.
- ✅ **Badges have each year's colour baked into the file.** The server produces one file per event (for example `varna-2026.svg`), so a badge looks right everywhere, including search results, previews and saved copies.
- ✅ **SVG to PNG happens in the editor's browser.** PHP's GD can't render SVG, so when an editor saves a badge or an event colour, the browser draws the coloured SVG and uploads PNG versions (favicons, home-screen icons, social previews) with it.
- ✅ **Processing:** PHP's built-in GD makes the sizes once, on upload. To keep within cheap hosts' memory limits, the editor's browser shrinks large photos to about 2,500 px before uploading.
- 🔜 **After launch, research AVIF:** a newer format, about 20–30% smaller than WebP and well supported by browsers, but slow to produce and often missing from GD on cheap hosts.
- ✅ **Sizes:** each photo is saved at about 400, 800 and 1,600 px wide, and the browser picks the right one (`srcset`).
- ✅ **Cleaning on upload:** hidden data such as GPS location and camera details is removed. Only JPEG, PNG, WebP and SVG are accepted, and SVG files are checked for scripts.
- ✅ **Alt text is required** in the admin, except for images marked as decorative.
- ✅ **Loading:** images below the first screen use the browser's built-in `loading="lazy"`, with no JavaScript. Every image has its width and height set, so the page doesn't jump while it loads.
- ✅ **Cropping:** when uploading a speaker photo (shown round), the editor drags and resizes a square crop box to frame the face. The crop is stored and the sizes are produced from it. The original stays untouched.

### Public back end

- ✅ **Data model (overview).** One SQLite database with these tables:
  - **cities:** name, domain, active
  - **events:** one per city per year; full name, date, colour, badge, venue (name, address, directions, map, latitude and longitude), after party (name, address, directions, map, start time, visibility switch), call for speakers deadline, live link
  - **slots:** event, start and end time, type (talk, break, other), title, and the talk if there is one
  - **talks:** title, description, video, slides, event
  - **talk_speakers:** links talks to speakers, so a talk can have several speakers
  - **speakers:** name, photo, one current bio, role and company, links, old ID (so old URLs keep working)
  - **sponsors** (name, logo, link, optional about) and **sponsorships** (sponsor, event or season homepage, free-text type, order)
  - **news, team_members, media**
  - **settings:** editor switches and texts
  - **redirects, missed_urls**
  - **admin_users**
- ✅ **PHP version: the latest stable release,** which is 8.5 as of November 2026. We move to each new release once it's out and our code passes on it. The host must offer the latest version.
- ✅ **Folder layout:**
  - `public/`: the only folder the web server exposes (`index.php`, CSS, JS, fonts, uploaded images)
  - `src/`: router, database code, helpers
  - `templates/`: pages, layouts, partials
  - `migrations/`: numbered SQL files that change the database step by step
  - `bin/`: command-line scripts (import, migrations, escaping check)
  - `data/`: the SQLite file and logs, outside `public/`
  - `vendor/`: Composer libraries, if any
- ✅ **We avoid Composer dependencies, and there is no `composer.json` until we actually need a library.** The project's requirements (PHP version, PHP extensions, any libraries) are still written down in one place for people to read; it doesn't have to be machine-readable.
