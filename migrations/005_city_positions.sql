-- Where each city is on the homepage map (decisions.md: "Map position" as
-- latitude and longitude, copied from Google Maps).
ALTER TABLE cities ADD COLUMN lat REAL;
ALTER TABLE cities ADD COLUMN lng REAL;
