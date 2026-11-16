<?php
/** Settings for the browser tests (bin/test starts a server with these on port 8001). */
return [
    'production' => false,
    'scheme' => 'http',
    'main_host' => 'it-tour.localhost:8001',
    'city_host' => '{city}.localhost:8001',
    'now' => null,
    'debug' => true,
];
