// Step 7: the it-tour.bg homepage in its three season states, and the season pop-up.
const { test, expect } = require('@playwright/test');
const { main, city, at, expectNoSidewaysScroll, expectAccessible, expectLooks } = require('./helpers');

test('during the season: next stop, map, numbers, videos', async ({ page }) => {
  await at(page, '2026-07-20 12:00');
  await page.goto(main('/'));
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Една конференция. Пет града. Всяко лято.');
  await expect(page.locator('.fact')).toHaveText(['59проведени конференции', 'от 2012всяка година', '491лекции', '221лектори']);
  // This season's badges: the first two stops are over.
  await expect(page.locator('.badges a')).toHaveCount(5);
  await expect(page.locator('.badges a.done')).toHaveCount(2);
  await expect(page.locator('.badges a').first()).toHaveAttribute('href', city('plovdivconf', '/'));
  // Stops: list and map agree.
  const stops = page.locator('#stops');
  await expect(stops.locator('.sec-head p')).toHaveText('Пет града, един на месец. Следващата спирка е Бургас.');
  await expect(stops.locator('.stop .badge')).toHaveText(['Мина', 'Мина', 'Следваща', 'Предстои', 'Предстои']);
  await expect(stops.locator('.stop.next h3')).toHaveText('BurgasConf');
  await expect(stops.locator('.node')).toHaveCount(5);
  await expect(stops.locator('.node.next text').first()).toHaveText('Бургас');
  await expect(stops.locator('.edge:not(.todo)')).toHaveAttribute('d', /^M382 463 L513 271 L790 388$/);
  await expect(stops.locator('.edge.todo')).toHaveAttribute('d', 'M790 388 L858 243 L565 116');
  // Recent videos: one per edition, newest first.
  await expect(page.locator('#videos .talk-card')).toHaveCount(4);
  // Nothing to show yet: no season sponsors, newsletter off.
  await expect(page.locator('#sponsors')).toHaveCount(0);
  await expect(page.locator('#subscribe')).toHaveCount(0);
  await expectNoSidewaysScroll(page);
  await expectAccessible(page);
  // The pop-up is fixed to the window, so it would land mid-page in a full-page picture.
  await page.getByRole('button', { name: 'Затвори' }).click();
  await expectLooks(page, 'home-upcoming.png');
});

test('the pop-up shows the next stop and stays closed once closed', async ({ page }) => {
  await at(page, '2026-07-20 12:00');
  await page.goto(main('/'));
  const popup = page.getByRole('complementary', { name: 'Статус на сезона' });
  await expect(popup).toContainText('Следваща спирка');
  await expect(popup.getByRole('heading')).toHaveText('BurgasConf · 25.07');
  await expect(popup).toContainText('След 5 дни');
  await expect(popup.getByRole('link', { name: 'Програмата' })).toHaveAttribute('href', city('burgasconf', '/#schedule'));
  await popup.getByRole('button', { name: 'Затвори' }).click();
  await expect(popup).toBeHidden();
  await page.goto(main('/talks'));
  await expect(page.locator('.status')).toBeHidden();
  // City sites show the event's own state instead.
  await page.goto(city('varnaconf', '/'));
  await expect(page.locator('.status')).toHaveCount(0);
});

test('while an event is live: watch button and what is on stage', async ({ page }) => {
  await at(page, '2026-07-25 11:00');
  await page.goto(main('/'));
  await expect(page.locator('.hero .btn-primary')).toHaveText('▶ На живо: BurgasConf 2026');
  await expect(page.locator('.hero .btn-primary')).toHaveAttribute('href', city('burgasconf', '/#live'));
  await expect(page.locator('#stops .sec-head p')).toContainText('Сега сме в Бургас');
  await expect(page.locator('.stop.next .badge')).toHaveText('На живо');
  const popup = page.getByRole('complementary', { name: 'Статус на сезона' });
  await expect(popup).toContainText('На живо сега');
  await expect(popup.locator('.now')).toContainText('На сцената:');
  await expect(popup.getByRole('link', { name: '▶ Гледай на живо' })).toBeVisible();
});

test('off-season: next dates are coming, no pop-up', async ({ page }) => {
  await at(page, '2026-11-16 12:00');
  await page.goto(main('/'));
  await expect(page.locator('.off-note')).toHaveText('Сезон 2026 завърши. Датите за 2027 предстоят.');
  await expect(page.locator('.hero .btn-primary')).toHaveText('Гледай лекциите');
  await expect(page.locator('.stop .badge')).toHaveText(['Мина', 'Мина', 'Мина', 'Мина', 'Мина']);
  await expect(page.locator('.edge.todo')).toHaveCount(0);
  await expect(page.locator('.status')).toHaveCount(0);
  await expectLooks(page, 'home-off.png', { fullPage: false });
});
