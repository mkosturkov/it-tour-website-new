// Step 6: the talks library (/talks) and speaker directory (/speakers) with instant search.
const { test, expect } = require('@playwright/test');
const { main, expectNoSidewaysScroll, expectAccessible, expectLooks } = require('./helpers');

const q = (s) => encodeURIComponent(s);

// Counts instant-search requests (the ones asking for the results block only).
function countPartials(page) {
  const seen = [];
  page.on('request', (r) => { if (r.headers()['x-partial'] === 'results') seen.push(r.url()); });
  return seen;
}

test('talks library: newest first, 24 per page, page links', async ({ page }) => {
  await page.goto(main('/talks'));
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Библиотека с лекции');
  await expect(page.locator('#main-nav a[aria-current=page]')).toHaveText('Лекции');
  await expect(page.locator('.count-line')).toHaveText('491 лекции · страница 1 от 21');
  await expect(page.locator('.talk-rows > a')).toHaveCount(24);
  await expect(page.locator('.talk-rows .when').first()).toContainText('2026');
  await expect(page.getByRole('navigation', { name: 'Страници' }).getByRole('link', { name: 'Напред →' })).toHaveAttribute('href', main('/talks?page=2'));
  await expectNoSidewaysScroll(page);
  await expectAccessible(page);
  await expectLooks(page, 'talks-top.png', { fullPage: false });
});

test('typing searches once after a pause; Back skips the words in between', async ({ page }) => {
  await page.goto(main('/talks'));
  const partials = countPartials(page);
  const box = page.getByLabel('Търси лекция или лектор');
  await box.pressSequentially('наков', { delay: 40 });
  await expect(page).toHaveURL(main('/talks?q=' + q('наков')));
  await expect(page.locator('.count-line')).toHaveText('12 лекции');
  expect(partials).toHaveLength(1);
  // More typing after a pause replaces the history entry instead of adding one.
  await box.press('End');
  await box.pressSequentially(' docker', { delay: 40 });
  await expect(page).toHaveURL(main('/talks?q=' + q('наков docker').replace(/%20/g, '+')));
  await page.goBack();
  await expect(page).toHaveURL(main('/talks'));
  await expect(box).toHaveValue('');
  await expect(page.locator('.count-line')).toHaveText('491 лекции · страница 1 от 21');
});

test('Latin finds Cyrillic: "nakov" finds the same talks as "наков"', async ({ page }) => {
  await page.goto(main('/talks?q=nakov'));
  await expect(page.locator('.count-line')).toHaveText('12 лекции');
  await expect(page.locator('.talk-rows > a').first()).toContainText('Светлин Наков');
});

test('filters add history entries; Back and Forward restore form and results', async ({ page }) => {
  await page.goto(main('/talks'));
  await page.evaluate(() => { window.notReloaded = true; });
  await page.getByLabel('Град').selectOption('varna');
  await expect(page).toHaveURL(main('/talks?city=varna'));
  await page.getByLabel('Година').selectOption('2024');
  await expect(page).toHaveURL(main('/talks?city=varna&year=2024'));
  await expect(page.locator('.count-line')).toHaveText('8 лекции');
  await page.getByLabel('Само със запис').check();
  await expect(page).toHaveURL(main('/talks?city=varna&year=2024&video=1'));
  await page.goBack();
  await page.goBack();
  await expect(page).toHaveURL(main('/talks?city=varna'));
  await expect(page.getByLabel('Година')).toHaveValue('');
  await expect(page.getByLabel('Само със запис')).not.toBeChecked();
  await page.goForward();
  await expect(page.getByLabel('Година')).toHaveValue('2024');
  await expect(page.locator('.count-line')).toHaveText('8 лекции');
  expect(await page.evaluate(() => window.notReloaded)).toBe(true);
});

test('page links load in place and can be shared', async ({ page }) => {
  await page.goto(main('/talks?city=varna'));
  await page.evaluate(() => { window.notReloaded = true; });
  await page.getByRole('link', { name: 'Страница 2' }).click();
  await expect(page).toHaveURL(main('/talks?city=varna&page=2'));
  await expect(page.locator('.count-line')).toContainText('страница 2 от');
  expect(await page.evaluate(() => window.notReloaded)).toBe(true);
  // The same address opened directly gives the same results.
  const first = await page.locator('.talk-rows h3').first().textContent();
  await page.goto(main('/talks?city=varna&page=2'));
  await expect(page.locator('.talk-rows h3').first()).toHaveText(first);
});

test('the result count is announced to screen readers', async ({ page }) => {
  await page.goto(main('/speakers'));
  await page.getByLabel('Търси по име').fill('nakov');
  await expect(page.getByRole('status')).toHaveText('2 лектори');
  await page.getByLabel('Търси по име').fill('zzzz');
  await expect(page.getByRole('status')).toContainText('Няма лектори');
  await expect(page.getByRole('link', { name: 'Изчисти филтрите' })).toHaveAttribute('href', main('/speakers'));
});

test('search works without JavaScript', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await page.goto(main('/talks'));
  await page.getByLabel('Търси лекция или лектор').fill('docker');
  await page.getByLabel('Само със запис').check();
  await page.getByRole('button', { name: 'Търси' }).click();
  await expect(page).toHaveURL(main('/talks?q=docker&city=&year=&video=1'));
  await expect(page.locator('.count-line')).toHaveText('3 лекции');
  await context.close();
});

test('speaker directory: alphabetical, cities, filters, search by name', async ({ page }) => {
  await page.goto(main('/speakers'));
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Лекторите на IT Tour');
  await expect(page.locator('#main-nav a[aria-current=page]')).toHaveText('Лектори');
  await expect(page.locator('.count-line')).toHaveText('221 лектори · страница 1 от 10');
  await expect(page.locator('.speakers .speaker')).toHaveCount(24);
  const usualWidth = (await page.locator('.speaker .face').first().boundingBox()).width;
  const names = await page.locator('.speaker b').allTextContents();
  expect(names).toEqual([...names].sort());
  await page.getByLabel('Година').selectOption('2024');
  await expect(page).toHaveURL(main('/speakers?year=2024'));
  await expect(page.locator('.speaker', { hasText: 'Светлин Наков' })).toHaveCount(0);
  await page.getByLabel('Година').selectOption('');
  await page.getByLabel('Град').selectOption('ruse');
  await page.getByLabel('Търси по име').fill('наков');
  await expect(page).toHaveURL(main('/speakers?q=' + q('наков') + '&city=ruse'));
  const nakov = page.locator('.speaker', { hasText: 'Светлин Наков' });
  await expect(nakov).toContainText('11 лекции · Пловдив, Велико Търново, Бургас, Варна, Русе');
  await expect(nakov).toHaveAttribute('href', main('/speakers/15'));
  // A single result keeps the usual card size instead of stretching across the row.
  await expect(page.locator('.speakers .speaker')).toHaveCount(1);
  expect((await nakov.locator('.face').boundingBox()).width).toBeCloseTo(usualWidth, 0);
  await page.goto(main('/speakers'));
  await expectNoSidewaysScroll(page);
  await expectAccessible(page);
  await expectLooks(page, 'speakers-top.png', { fullPage: false });
});
