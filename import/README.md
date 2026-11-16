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
