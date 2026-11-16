<?php
/**
 * Settings for one installation. Copy to config.php (not committed) and adjust.
 * See docs/decisions.md, "Domains are set per installation".
 */

return [
    // Production: real domains, indexable by search engines.
    // Anything else: every page is "noindex" and robots.txt blocks everything.
    'production' => false,

    // http or https for the links the site builds.
    'scheme' => 'http',

    // Host of the main site (it-tour.bg in production), with the port if any.
    'main_host' => 'it-tour.localhost:8000',

    // Hosts of the city sites. {city} is the city's production domain without
    // the ending: varnaconf.com → varnaconf. Use null in production to use each
    // city's own domain from the database.
    'city_host' => '{city}.localhost:8000',
    // Demo example:
    // 'main_host' => 'demo.example.com',
    // 'city_host' => '{city}.demo.example.com',

    // Fake the current time, for testing the upcoming / live / finished states.
    // Bulgarian time, for example '2026-08-22 11:30'. Leave null normally.
    // The IT_TOUR_NOW environment variable overrides it.
    'now' => null,

    // Show PHP error details in the page instead of the plain error page.
    // Never true on a public installation.
    'debug' => true,
];
