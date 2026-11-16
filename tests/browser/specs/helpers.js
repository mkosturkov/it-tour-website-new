// Shared helpers for the browser tests.
const { expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;
const fs = require('fs');
const path = require('path');

const CONTRAST_REPORT = path.join(__dirname, '..', 'reports', 'contrast.txt');

const main = (path = '/') => `http://it-tour.localhost:8001${path}`;
const city = (key, path = '/') => `http://${key}.localhost:8001${path}`;

// The page must not scroll sideways (phones especially).
async function expectNoSidewaysScroll(page) {
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
  expect(overflow, 'page is wider than the screen').toBeLessThanOrEqual(0);
}

// Automatic WCAG 2.2 AA checks with axe. Colour contrast is not a failure: the colours come
// from editors, so contrast problems are written to reports/contrast.txt for people to review.
async function expectAccessible(page) {
  const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa']).analyze();
  const contrast = results.violations.filter((v) => v.id === 'color-contrast');
  const others = results.violations.filter((v) => v.id !== 'color-contrast');
  if (contrast.length) {
    const lines = contrast.flatMap((v) => v.nodes.map((n) => `${page.url()}  ${n.target.join(' ')}  ${n.any.map((a) => a.message).join(' ')}`));
    fs.mkdirSync(path.dirname(CONTRAST_REPORT), { recursive: true });
    fs.appendFileSync(CONTRAST_REPORT, lines.join('\n') + '\n');
  }
  const summary = others.map((v) => `${v.id}: ${v.help} (${v.nodes.length}) e.g. ${v.nodes[0].target}`);
  expect(summary, 'accessibility problems').toEqual([]);
}

// Fake the current time (Bulgarian time) for the following requests.
async function at(page, when) {
  await page.setExtraHTTPHeaders({ 'X-Test-Now': when });
}

module.exports = { main, city, expectNoSidewaysScroll, expectAccessible, at };
