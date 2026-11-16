-- Livestream recordings (from 2024 the talks were only livestreamed).
-- talks.video_start: seconds into the video where the talk starts (null: from the beginning).
-- talks.video_start_approx: 1 when that start was worked out from the programme
--   time rather than set by a person; the admin shows these to editors to check.
-- events.recordings: JSON list of YouTube addresses of the full-day recording(s), in order.
ALTER TABLE talks ADD COLUMN video_start INTEGER;
ALTER TABLE talks ADD COLUMN video_start_approx INTEGER NOT NULL DEFAULT 0;
ALTER TABLE events ADD COLUMN recordings TEXT NOT NULL DEFAULT '[]';
