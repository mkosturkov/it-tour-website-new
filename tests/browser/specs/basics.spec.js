// Step 1: the frame of every page, the 404 page and redirects.
const { test, expect } = require('@playwright/test');
const { main, city, expectNoSidewaysScroll, expectAccessible } = require('./helpers');

test.describe('404 page', () => {
  test('unknown address on a city site gives the styled 404', async ({ page }) => {
    const res = await page.goto(city('varnaconf', '/no-such-page'));
    expect(res.status()).toBe(404);
    await expect(page.getByRole('heading', { level: 1 })).toHaveText('Тази страница я няма');
    // This season's events link to their city sites.
    const varna = page.getByRole('link', { name: /VarnaConf 2026/ });
    await expect(varna).toHaveAttribute('href', city('varnaconf'));
    await expect(page.getByRole('link', { name: 'Всички лекции' })).toHaveAttribute('href', main('/talks'));
    // No search box.
    await expect(page.getByRole('searchbox')).toHaveCount(0);
    await expectNoSidewaysScroll(page);
    await expectAccessible(page);
    await expect(page).toHaveScreenshot('404-varna.png', { fullPage: true });
  });

  test('city sites use the city colour, the main site the hub accent', async ({ page }) => {
    await page.goto(city('varnaconf', '/x'));
    await expect(page.locator('html')).toHaveClass(/city/);
    await page.goto(main('/x'));
    await expect(page.locator('html')).toHaveClass(/hub/);
  });
});

test.describe('frame', () => {
  test('menu links go to the main site from every domain', async ({ page }) => {
    await page.goto(city('burgasconf', '/'));
    // On phones the menu is folded away, so look at the links whether visible or not.
    const links = page.locator('#main-nav a');
    await expect(links).toHaveText(['Спирки', 'Лекции', 'Лектори', 'Архив', 'Екип']);
    await expect(links.filter({ hasText: 'Лекции' })).toHaveAttribute('href', main('/talks'));
  });

  test('footer lists the active cities and contact', async ({ page }) => {
    await page.goto(main('/'));
    const footer = page.locator('footer');
    await expect(footer.getByRole('link', { name: /Conf$/ })).toHaveText(['PlovdivConf', 'TarnovoConf', 'BurgasConf', 'VarnaConf', 'RuseConf']);
    await expect(footer.getByRole('link', { name: 'info@it-tour.bg' })).toHaveAttribute('href', 'mailto:info@it-tour.bg');
  });

  test('phone menu opens and closes', async ({ page, isMobile }) => {
    test.skip(!isMobile, 'phone only');
    await page.goto(main('/'));
    const button = page.getByRole('button', { name: 'Меню' });
    await expect(page.locator('#main-nav a', { hasText: 'Лекции' })).toBeHidden();
    await button.click();
    await expect(button).toHaveAttribute('aria-expanded', 'true');
    await expect(page.getByRole('navigation').getByRole('link', { name: 'Лекции' })).toBeVisible();
  });

  test('not indexed outside production', async ({ page }) => {
    const res = await page.goto(main('/'));
    expect(res.headers()['x-robots-tag']).toContain('noindex');
    await expect(page.locator('meta[name=robots]')).toHaveAttribute('content', /noindex/);
  });
});

test('www. redirects permanently to the address without it', async ({ request }) => {
  const res = await request.get('http://www.varnaconf.localhost:8001/archive/2015', { maxRedirects: 0 });
  expect(res.status()).toBe(301);
  expect(res.headers()['location']).toBe('http://varnaconf.localhost:8001/archive/2015');
});
