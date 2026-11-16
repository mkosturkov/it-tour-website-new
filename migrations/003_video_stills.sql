-- A still image for each talk video, stored in our media library so pages
-- don't load images from YouTube before the visitor clicks play.
ALTER TABLE talks ADD COLUMN video_still_media_id INTEGER REFERENCES media(id);
