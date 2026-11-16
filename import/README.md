# Import data kept in the repository

Files here are inputs to `bin/import` that can't be recreated from the old sites, so they are committed and a new import never has to redo the work.

## `video-matches.csv`

Talks that the old sites didn't link to a video, matched to recordings on the IT Tour YouTube channel by `bin/match-videos` (2026-11-16).

| Column | Meaning |
|---|---|
| `edition` | city domain and year, for example `varnaconf.com/2023` |
| `time` | start time of the talk's slot |
| `title` | talk title (or the slot title for lightning talks) |
| `youtube_id` | the matched video, `https://www.youtube.com/watch?v=<id>` |
| `video_title` | the video's title on YouTube, to compare by eye |
| `source` | `playlist` (from the edition's own playlist) or `channel` (searched across the whole channel) |
| `score` | title similarity, 0–100 |
| `status` | `sure`, `likely` or `check` as set by the matcher; change it to `ok` after checking, or `wrong` to drop the match |

**To review:** open the file in a spreadsheet, compare `title` with `video_title`, start with the `check` and `likely` rows, and set `status`. Then run `bin/import`. Rows marked `wrong` are ignored; `bin/match-videos` keeps existing rows as they are and only adds new ones.

Lightning talk slots with a matched recording become talks named "Lightning talks" on import, as on the old site.

### Approximate video starts (parked, to resolve after the build)

Rows with status `approximate` link a talk into a full-day livestream recording at a start time **calculated** from the programme time and the stream's real start time; real talks often started a few minutes later (measured on VarnaConf 2024: from half a minute to 6 minutes late). These talks are marked in the database (`talks.video_start_approx = 1`) and say "приблизително" on their page.

Until the admin can do it, a person can correct one here: find the real start in the recording, put it in `start` (seconds into the video) and change `status` to `ok`. `bin/import` then treats the start as exact (`video_start_approx = 0`). Alternatively, add YouTube chapters to the recording and run `bin/match-videos` again for those rows (delete them first).

**Parked (2026-11-16):** how and when these get checked is to be resolved after the coding is done; the admin should list marked talks for editors (see `docs/decisions.md`).
