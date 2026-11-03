# IT Tour website: decisions and open questions

Status: homepage feedback given. Version E (dark, about-first, status pop-up) built for review.
Last updated: 2026-10-27

Legend: ✅ decided · ❓ open, to discuss · 🔜 later

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
  5. **Numbers.** Years, editions, talks, speakers.
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
  - 🔜 The form fields are still to be decided. The placeholder asks for name, email, city, and whether you want to help the whole tour or one city.
- ✅ **Team:** its own page, because the team may grow large.
  - ❓ **Which layout?** The mockups show both. A: grouped into the core team, then each city's team. B: one flat list with role and city labels.
  - City pages also show their local team.
- ✅ **News:** the latest items on the homepage plus a news page. It covers short announcements and longer write-ups after events, with an optional city tag so city news also shows on that city's page.
- ✅ **Become a speaker:** explanation plus a placeholder application form.
- ✅ **For sponsors:** why sponsor, audience numbers, what sponsors get, a contact button. No price list.
- ✅ **About:** the IT Tour story since 2012.
- ✅ **Code of conduct.**

## 7. Attendance and registration

- ✅ **Optional registration.** An "I'm coming" button with name and email. Entry stays free and nobody is turned away; registration gives us a headcount and lets us send reminders.
- ❓ **How do we get people to register?** To discuss with the team. Ideas so far:
  - A reminder the day before, plus the stream link if they can't make it.
  - Faster entry at the door, a name badge printed in advance.
  - A raffle among registered people, with prizes from sponsors.
  - Slides and videos emailed to them after the event.
  - A "N people are coming" counter on the event page.
  - A calendar invite they can add in one click.
  - Priority for limited extras: after-party spots, workshops, swag.

## 8. Branding and photos

- ✅ **City events keep their own logos and colours**, and every event page clearly shows it is part of IT Tour, for example with an "IT Tour" mark and links to the other cities.
- ✅ **Photos:** past event photos probably exist, but nobody has access right now. The mockups use placeholder images.
- 🔜 Find and collect the past photos. Consider a photographer for 2027.

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

1. Team page: grouped or flat list? Look at both mockups.
2. Registration is optional. Which of the incentive ideas in section 7 do we want to use?
3. Photos: who has the past event photos, and can we use them?
4. Volunteer form: which fields do we need?
5. Sponsors: are we sure about no public prices?
6. Numbers: the mockups say 230+ speakers and 350+ talks. These are estimates (from the highest speaker ID on the old site, and roughly 6 talks per edition). What are the real counts? The 62 editions and the 2012 start are exact.
7. Accent colour for version E. Try the switch in the top right of designs/e-tour-dark (blue, green, violet, teal, white; 1 = soft, higher = more vivid). Default is Blue 2.
8. E or F? Compare `designs/e-tour-dark` and `designs/f-brand`.
9. The "Back In The USSR DL" font came without a licence file. Can we use it on a public website?


## Version F (brand visuals)
- ✅ Built as `designs/f-brand`. It has every page and switch from E, styled after the IT Tour 2026 social media visuals.
- ✅ The real IT Tour logo and the city badges come from the Figma exports.
- ✅ Each conference page uses its city colour. The VarnaConf visuals are mint (#6ec3aa).
- ✅ City colours updated to the badge values: Plovdiv #c8cc05, Tarnovo #f38c16, Burgas #f18a85, Varna #6ec3aa, Ruse #019295.
- ❓ E (dark) or F (light, brand)?
- ✅ Wordmark font "Back In The USSR DL" added. It has Latin letters only, so it is used for the Latin wordmarks.
- ❓ The font came without a licence file. We need to confirm it can be used on a website.

## Homepage feedback (round 1)
- ✅ C (Editorial) dropped. A: clean and easy to follow, but no paper or road-trip look. B: colours liked, terminal style too generic. D: section order confusing.
- ✅ Homepage order: what IT Tour is → stops (map with dates + city cards) → news → team → videos → get involved → sponsors → subscribe.
- ✅ Season status is a small pop-up that slides in after load (next stop / live now). No pop-up off-season.
- ✅ Dark theme, a bit lighter than B, not terminal-styled. Built as version E (designs/e-tour-dark).
