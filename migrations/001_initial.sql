-- Initial schema. See docs/decisions.md, "Public back end".
-- Times are local Bulgarian time. Dates are YYYY-MM-DD, times HH:MM.

CREATE TABLE cities (
    id          INTEGER PRIMARY KEY,
    slug        TEXT NOT NULL UNIQUE,           -- varna
    name        TEXT NOT NULL,                  -- Варна
    domain      TEXT NOT NULL UNIQUE,           -- varnaconf.com
    sort_order  INTEGER NOT NULL DEFAULT 0,     -- order in the season
    active      INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE media (
    id          INTEGER PRIMARY KEY,
    hash        TEXT NOT NULL UNIQUE,           -- sha1 of the original file
    name        TEXT NOT NULL,                  -- slug used in file names
    mime        TEXT NOT NULL,                  -- of the original
    ext         TEXT NOT NULL,                  -- of the original
    width       INTEGER,                        -- of the original (null for SVG)
    height      INTEGER,
    crop        TEXT,                           -- JSON {x,y,w,h} in original pixels, or null
    widths      TEXT NOT NULL DEFAULT '[]',     -- JSON list of produced WebP widths
    alt         TEXT NOT NULL DEFAULT '',
    decorative  INTEGER NOT NULL DEFAULT 0,
    source      TEXT,                           -- where it came from (old URL on import)
    created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE events (
    id                  INTEGER PRIMARY KEY,
    city_id             INTEGER NOT NULL REFERENCES cities(id),
    year                INTEGER NOT NULL,
    name                TEXT NOT NULL,          -- VarnaConf 2024
    date                TEXT NOT NULL,
    starts_at           TEXT NOT NULL DEFAULT '10:30',
    ends_at             TEXT NOT NULL DEFAULT '18:00',
    colour              TEXT,                   -- #E09D4E
    logo_media_id       INTEGER REFERENCES media(id),
    cover_media_id      INTEGER REFERENCES media(id),

    venue_name          TEXT,
    venue_url           TEXT,
    venue_address       TEXT,
    venue_directions    TEXT,
    venue_map_url       TEXT,                   -- Google Maps embed address
    venue_lat           REAL,
    venue_lng           REAL,

    party_name          TEXT,
    party_url           TEXT,
    party_address       TEXT,
    party_directions    TEXT,
    party_map_url       TEXT,
    party_starts_at     TEXT,
    party_visible       INTEGER NOT NULL DEFAULT 0,

    cfs_deadline        TEXT,                   -- call for speakers deadline
    live_url            TEXT,                   -- live stream embed
    stream_url          TEXT,                   -- direct address for external players
    facebook_url        TEXT,

    published           INTEGER NOT NULL DEFAULT 0,
    schedule_published  INTEGER NOT NULL DEFAULT 0,
    old_id              INTEGER,
    UNIQUE (city_id, year)
);

CREATE TABLE speakers (
    -- Imported speakers keep their old numbers as id, so /speakers/<id> keeps working.
    id              INTEGER PRIMARY KEY,
    name            TEXT NOT NULL,
    photo_media_id  INTEGER REFERENCES media(id),
    bio             TEXT NOT NULL DEFAULT '',
    role            TEXT,                       -- job title
    company         TEXT,
    links           TEXT NOT NULL DEFAULT '[]', -- JSON list of URLs
    search_text     TEXT NOT NULL DEFAULT '',
    published       INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE talks (
    id           INTEGER PRIMARY KEY,
    event_id     INTEGER NOT NULL REFERENCES events(id),
    title        TEXT NOT NULL,
    slug         TEXT NOT NULL,
    description  TEXT NOT NULL DEFAULT '',
    video_url    TEXT,
    slides_url   TEXT,
    search_text  TEXT NOT NULL DEFAULT '',
    published    INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX talks_event ON talks(event_id);

CREATE TABLE talk_speakers (
    talk_id     INTEGER NOT NULL REFERENCES talks(id) ON DELETE CASCADE,
    speaker_id  INTEGER NOT NULL REFERENCES speakers(id),
    sort_order  INTEGER NOT NULL DEFAULT 0,
    PRIMARY KEY (talk_id, speaker_id)
);
CREATE INDEX talk_speakers_speaker ON talk_speakers(speaker_id);

CREATE TABLE slots (
    id          INTEGER PRIMARY KEY,
    event_id    INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    starts_at   TEXT NOT NULL,
    ends_at     TEXT,
    kind        TEXT NOT NULL CHECK (kind IN ('talk', 'break', 'other')),
    title       TEXT NOT NULL DEFAULT '',       -- for breaks and other slots
    talk_id     INTEGER REFERENCES talks(id) ON DELETE SET NULL
);
CREATE INDEX slots_event ON slots(event_id, starts_at);

CREATE TABLE sponsors (
    id             INTEGER PRIMARY KEY,
    name           TEXT NOT NULL,
    logo_media_id  INTEGER REFERENCES media(id),
    url            TEXT,
    about          TEXT,
    published      INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE sponsorships (
    id          INTEGER PRIMARY KEY,
    sponsor_id  INTEGER NOT NULL REFERENCES sponsors(id) ON DELETE CASCADE,
    event_id    INTEGER REFERENCES events(id) ON DELETE CASCADE,  -- null: season homepage
    year        INTEGER,                                          -- set for season homepage
    type        TEXT NOT NULL DEFAULT '',                         -- free text: "Златни"
    sort_order  INTEGER NOT NULL DEFAULT 0,
    CHECK ((event_id IS NULL) <> (year IS NULL))
);

CREATE TABLE news (
    id            INTEGER PRIMARY KEY,
    title         TEXT NOT NULL,
    slug          TEXT NOT NULL,
    body          TEXT NOT NULL DEFAULT '',
    image_media_id INTEGER REFERENCES media(id),
    published_at  TEXT,
    published     INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE team_members (
    id              INTEGER PRIMARY KEY,
    name            TEXT NOT NULL,
    photo_media_id  INTEGER REFERENCES media(id),
    role            TEXT,
    about           TEXT,
    sort_order      INTEGER NOT NULL DEFAULT 0,
    published       INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE settings (
    key    TEXT PRIMARY KEY,
    value  TEXT NOT NULL
);

CREATE TABLE redirects (
    id        INTEGER PRIMARY KEY,
    host      TEXT NOT NULL DEFAULT '',     -- empty: any domain
    path      TEXT NOT NULL,
    target    TEXT NOT NULL,
    UNIQUE (host, path)
);

CREATE TABLE missed_urls (
    host       TEXT NOT NULL,
    path       TEXT NOT NULL,
    hits       INTEGER NOT NULL DEFAULT 1,
    last_seen  TEXT NOT NULL DEFAULT (datetime('now')),
    PRIMARY KEY (host, path)
);

CREATE TABLE admin_users (
    id             INTEGER PRIMARY KEY,
    email          TEXT NOT NULL UNIQUE,
    name           TEXT NOT NULL,
    password_hash  TEXT NOT NULL,
    created_at     TEXT NOT NULL DEFAULT (datetime('now'))
);
