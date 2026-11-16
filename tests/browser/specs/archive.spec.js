// Step 4: it-tour.bg/archive, every edition by year, filtered by city and year.
const { test, expect } = require('@playwright/test');
const { main, city, expectNoSidewaysScroll, expectAccessible, expectLooks } = require('./helpers');

test('lists all 62 editions, newest year first', async ({ page }) => {
  await page.goto(main('/archive'));
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Всички издания от 2012 насам');
  await expect(page.locator('.ed')).toHaveCount(62);
  await expect(page.locator('.year-head h2').first()).toHaveText('2026');
  await expect(page.locator('.year-head h2').last()).toHaveText('2012');
  // Cards link to the edition on its city site and show the numbers.
  const varna2024 = page.locator('.ed', { hasText: 'VarnaConf 2024' });
  await expect(varna2024).toHaveAttribute('href', city('varnaconf', '/archive/2024'));
  await expect(varna2024).toContainText('17.08 · Хотел Черно море');
  await expect(varna2024).toContainText('8 лекции');
  await expect(page.locator('#main-nav a[aria-current=page]')).toHaveText('Архив');
  await expectNoSidewaysScroll(page);
  await expectAccessible(page);
  await expectLooks(page, 'archive-top.png', { fullPage: false });
});

test('filter by city with the pills', async ({ page }) => {
  await page.goto(main('/archive'));
  const pills = page.getByRole('group', { name: 'Град' });
  await pills.getByRole('link', { name: 'Варна', exact: true }).click();
  await expect(page).toHaveURL(main('/archive?city=varna'));
  await expect(page.locator('.ed')).toHaveCount(13);
  await expect(pills.getByRole('link', { name: 'Варна', exact: true })).toHaveAttribute('aria-current', 'true');
});

test('filter by year: the select submits by itself', async ({ page }) => {
  await page.goto(main('/archive?city=varna'));
  await expect(page.getByRole('button', { name: 'Покажи' })).toBeHidden();
  await page.getByLabel('Година').selectOption('2015');
  await expect(page).toHaveURL(main('/archive?city=varna&year=2015'));
  await expect(page.locator('.ed')).toHaveCount(1);
});

test('filters work without JavaScript', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await page.goto(main('/archive'));
  await page.getByLabel('Година').selectOption('2013');
  await page.getByRole('button', { name: 'Покажи' }).click();
  await expect(page).toHaveURL(main('/archive?year=2013'));
  await expect(page.locator('.ed')).toHaveCount(5);
  await context.close();
});

test('a choice with no editions says so; unknown values are ignored', async ({ page }) => {
  await page.goto(main('/archive?city=blagoevgrad&year=2024'));
  await expect(page.locator('.empty')).toContainText('Няма издания');
  await page.goto(main('/archive?city=sofia&year=1999'));
  await expect(page.locator('.ed')).toHaveCount(62);
});
