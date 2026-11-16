// Step 3: the edition page on a city site, in its three states.
const { test, expect } = require('@playwright/test');
const { city, main, at, expectNoSidewaysScroll, expectAccessible } = require('./helpers');

test.describe('a finished edition (VarnaConf 2024)', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(city('varnaconf', '/archive/2024'));
  });

  test('shows the edition with its own colour, programme and speakers', async ({ page }) => {
    await expect(page).toHaveTitle('VarnaConf 2024');
    await expect(page.getByRole('heading', { level: 1 })).toHaveText('VarnaConf 2024');
    await expect(page.locator('html')).toHaveAttribute('style', /--c:#e09d4e/);
    await expect(page.locator('.state-chip')).toHaveText('Събитието завърши');
    await expect(page.locator('.ev-facts')).toContainText('Събота, 17 август 2024');
    await expect(page.locator('.ev-facts')).toContainText('Хотел Черно море');

    // Programme: 7 talks among the slots, linked to their talk pages on the main site.
    const talks = page.locator('#schedule .slot:not(.break) h3 a');
    await expect(talks).toHaveCount(7);
    await expect(talks.first()).toHaveText('Два проекта с отворен код, които ще променят живота ти!');
    await expect(talks.first()).toHaveAttribute('href', new RegExp('^' + main('/talks/\\d+-dva-proekta')));
    // English titles are marked for screen readers.
    await expect(page.locator('#schedule h3', { hasText: 'Microservices done right' })).toHaveAttribute('lang', 'en');

    // Speakers: one card per speaker, profile on this city site.
    const speakers = page.locator('#speakers .speaker');
    await expect(speakers).toHaveCount(7);
    await expect(speakers.first()).toHaveAttribute('href', city('varnaconf', '/speakers/161'));

    // Finished: numbers instead of a countdown.
    await expect(page.locator('.ev-stats')).toContainText('7 лекции');
    await expect(page.locator('[data-countdown]')).toHaveCount(0);
  });

  test('venue, after party (not in the schedule) and sponsors by type', async ({ page }) => {
    await expect(page.locator('#venue')).toContainText('бул. „Сливница“ 33, Варна');
    await expect(page.locator('#after-party')).toContainText("Flame's Bar");
    await expect(page.locator('#after-party')).toContainText('От 20:00');
    await expect(page.locator('#schedule')).not.toContainText(/афтър|after party/i);
    await expect(page.locator('#partners .tier .t')).toHaveText(['Златни', 'Сребърни', 'Партньори', 'С медийната подкрепа на']);
    await expect(page.locator('#partners img[alt="DataArt"]')).toBeVisible();
  });

  test('maps load only on click', async ({ page }) => {
    await expect(page.locator('#venue iframe')).toHaveCount(0);
    await page.locator('#venue').getByRole('link', { name: 'Покажи картата' }).click();
    await expect(page.locator('#venue iframe')).toHaveAttribute('src', /maps\.google\.com/);
  });

  test('links to other stops and past editions', async ({ page }) => {
    await expect(page.locator('#other-stops .stop')).toHaveCount(4);
    await expect(page.locator('#editions').getByRole('link', { name: '2012' })).toHaveAttribute('href', city('varnaconf', '/archive/2012'));
  });

  test('looks right and is accessible', async ({ page }) => {
    await expectNoSidewaysScroll(page);
    await expectAccessible(page);
    await expect(page).toHaveScreenshot('varna-2024.png', { fullPage: true });
  });
});

test('the city homepage shows the latest edition, canonical to "/"', async ({ page }) => {
  await page.goto(city('varnaconf', '/'));
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('VarnaConf 2026');
  await expect(page.locator('link[rel=canonical]')).toHaveAttribute('href', city('varnaconf', '/'));
  await page.goto(city('varnaconf', '/archive/2026'));
  await expect(page.locator('link[rel=canonical]')).toHaveAttribute('href', city('varnaconf', '/'));
});

test('an edition that does not exist is a 404', async ({ page }) => {
  const res = await page.goto(city('varnaconf', '/archive/2020'));
  expect(res.status()).toBe(404);
});

test('parallel workshops are marked (VarnaConf 2015)', async ({ page }) => {
  await page.goto(city('varnaconf', '/archive/2015'));
  await expect(page.locator('#schedule .slot', { hasText: 'GraphQL' }).locator('.parallel')).toHaveText('паралелно');
  // The shared lunch appears once.
  await expect(page.locator('#schedule .slot.break', { hasText: 'Обяд' })).toHaveCount(1);
});

test.describe('VarnaConf 2026 through the day (22 August 2026)', () => {
  test('a month before: countdown', async ({ page }) => {
    await at(page, '2026-07-22 10:30');
    await page.goto(city('varnaconf', '/'));
    await expect(page.locator('.state-chip')).toHaveText('Програмата е публикувана');
    await expect(page.locator('[data-countdown] [data-unit=d]')).toHaveText('31');
    await expect(page).toHaveScreenshot('varna-2026-upcoming.png');
  });

  test('during a talk: live, with the current talk marked', async ({ page }) => {
    await at(page, '2026-08-22 11:20');
    await page.goto(city('varnaconf', '/'));
    await expect(page.locator('.state-chip')).toHaveText('На живо сега');
    const now = page.locator('#schedule .slot.now');
    await expect(now).toHaveCount(1);
    await expect(page.locator('.onstage-now')).toContainText(await now.locator('h3').innerText());
    await expect(page.locator('#schedule .slot.past').first()).toBeVisible();
  });

  test('in the evening: finished', async ({ page }) => {
    await at(page, '2026-08-22 20:30');
    await page.goto(city('varnaconf', '/'));
    await expect(page.locator('.state-chip')).toHaveText('Събитието завърши');
  });
});

test('every edition page opens without errors', async ({ page, request }, testInfo) => {
  test.skip(testInfo.project.name === 'phone', 'once is enough');
  const editions = {
    plovdivconf: [2013, 2014, 2015, 2016, 2017, 2018, 2019, 2022, 2023, 2024, 2025, 2026],
    varnaconf: [2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2022, 2023, 2024, 2025, 2026],
    blagoevgradconf: [2013, 2014],
  };
  for (const [key, years] of Object.entries(editions)) {
    for (const year of years) {
      const res = await request.get(city(key, `/archive/${year}`));
      expect(res.status(), `${key} ${year}`).toBe(200);
      const body = await res.text();
      expect(body, `${key} ${year}`).not.toMatch(/Warning|Notice|Deprecated|Exception/);
    }
  }
});
