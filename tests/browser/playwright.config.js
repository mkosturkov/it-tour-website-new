// Browser tests. bin/test runs them; they start their own PHP server on port 8001
// with tests/config.php and the frozen test database (data/test/site.sqlite).
const { defineConfig, devices } = require('@playwright/test');
const path = require('path');

const root = path.resolve(__dirname, '../..');

module.exports = defineConfig({
  testDir: './specs',
  snapshotPathTemplate: '{testDir}/../screenshots/{testFilePath}/{arg}-{projectName}{ext}',
  fullyParallel: true,
  globalSetup: './global-setup.js',
  reporter: [['list']],
  expect: {
    // Small rendering differences (anti-aliasing) are not changes.
    toHaveScreenshot: { maxDiffPixelRatio: 0.01, animations: 'disabled' },
  },
  use: {
    // Every test fakes "now" unless it sets its own (see helpers.js).
    extraHTTPHeaders: { 'X-Test-Now': '2026-11-16 12:00' },
  },
  projects: [
    { name: 'desktop', use: { ...devices['Desktop Chrome'], viewport: { width: 1400, height: 900 } } },
    { name: 'phone', use: { ...devices['Pixel 7'], viewport: { width: 390, height: 844 } } },
  ],
  webServer: {
    command: `php -S localhost:8001 -t ${root}/public ${root}/public/index.php`,
    url: 'http://it-tour.localhost:8001/',
    reuseExistingServer: false,
    stdout: 'ignore',
    stderr: 'ignore',   // request lines; PHP errors go to data/logs/
    env: {
      IT_TOUR_CONFIG: `${root}/tests/config.php`,
      IT_TOUR_DB: `${root}/data/test/site.sqlite`,
      IT_TOUR_TESTING: '1',
    },
  },
});
