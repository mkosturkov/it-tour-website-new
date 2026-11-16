// Step 5: speaker profiles (every domain) and talk pages (main site).
const { test, expect } = require('@playwright/test');
const { main, city, expectNoSidewaysScroll, expectAccessible } = require('./helpers');

test.describe('speaker profile', () => {
  test('shows bio, links and all talks, newest first', async ({ page }) => {
    await page.goto(main('/speakers/127'));
    await expect(page.getByRole('heading', { level: 1 })).toHaveText('Стефан Ангелов');
    await expect(page.locator('.profile .eyebrow')).toHaveText('Лектор на IT Tour · 5 лекции');
    await expect(page.locator('.bio')).toContainText('софтуерен архитект в Trading212');
    // Broken old links are cleaned: twitter.com/https://x.com/cefothe → x.com/cefothe
    await expect(page.locator('ul.links a')).toHaveText(['LinkedIn ↗', 'X ↗', 'Facebook ↗']);
    await expect(page.locator('ul.links a', { hasText: 'X' })).toHaveAttribute('href', 'https://x.com/cefothe');
    const cards = page.locator('.talk-card');
    await expect(cards).toHaveCount(5);
    await expect(cards.first()).toContainText('PlovdivConf 2025');
    await expect(cards.last()).toContainText('EIP with Apache Camel');
    await expect(page.locator('#main-nav a[aria-current=page]')).toHaveText('Лектори');
    await expectNoSidewaysScroll(page);
    await expectAccessible(page);
    await expect(page).toHaveScreenshot('speaker-127.png', { fullPage: true });
  });

  test('old city addresses keep working, canonical to it-tour.bg', async ({ page }) => {
    const res = await page.goto(city('varnaconf', '/speakers/127'));
    expect(res.status()).toBe(200);
    await expect(page.locator('link[rel=canonical]')).toHaveAttribute('href', main('/speakers/127'));
    // City copies keep the city's look.
    await expect(page.locator('html')).toHaveClass(/city/);
  });

  test('a speaker without a photo gets an initial', async ({ page }) => {
    await page.goto(main('/speakers/85'));
    await expect(page.locator('.profile .no-photo')).toHaveText('A');
  });

  test('unknown speakers are a 404', async ({ page }) => {
    expect((await page.goto(main('/speakers/99999'))).status()).toBe(404);
  });
});

test.describe('talk page', () => {
  test('video loads on click, from youtube-nocookie, with our own still image', async ({ page }) => {
    await page.goto(main('/talks/179-testing-rest-services-with-rest-assured'));
    await expect(page.getByRole('heading', { level: 1 })).toHaveText('Testing REST services with REST-Assured');
    await expect(page.getByRole('heading', { level: 1 })).toHaveAttribute('lang', 'en');
    await expect(page.locator('.talk-meta')).toContainText('PlovdivConf 2017');
    await expect(page.locator('.talk-by')).toHaveText('Георги Иванов');
    // Nothing from YouTube or Google before the click.
    const external = [];
    page.on('request', (r) => { if (!r.url().includes('localhost')) external.push(r.url()); });
    await page.reload();
    await expect(page.locator('.embed img')).toHaveAttribute('src', /^\/media\//);
    expect(external).toEqual([]);
    // Two choices on the still: watch on YouTube (a plain link), or load the player here.
    await expect(page.getByRole('link', { name: 'Гледай в YouTube ↗' })).toHaveAttribute('href', /^https:\/\/www\.youtube\.com\/watch\?v=[\w-]{11}$/);
    await page.getByRole('link', { name: 'Гледай тук' }).click();
    // No autoplay: YouTube counts a view only when its own play button starts the video.
    await expect(page.locator('.embed iframe')).toHaveAttribute('src', /^https:\/\/www\.youtube-nocookie\.com\/embed\/[\w-]{11}$/);
  });

  test('other talks of the edition and a way back to it', async ({ page }) => {
    await page.goto(main('/talks/179-testing-rest-services-with-rest-assured'));
    await expect(page.locator('.aside-list a')).toHaveCount(7);   // 6 talks + the lightning talks recording
    await expect(page.getByRole('link', { name: 'Към изданието' })).toHaveAttribute('href', city('plovdivconf', '/archive/2017'));
    await expectNoSidewaysScroll(page);
    await expectAccessible(page);
    await expect(page).toHaveScreenshot('talk-179.png');
  });

  test('the number finds the talk; a wrong or missing slug redirects', async ({ request }) => {
    for (const path of ['/talks/179', '/talks/179-old-title']) {
      const res = await request.get(main(path), { maxRedirects: 0 });
      expect(res.status(), path).toBe(301);
      expect(res.headers()['location']).toBe(main('/talks/179-testing-rest-services-with-rest-assured'));
    }
    expect((await request.get(main('/talks/99999-x'))).status()).toBe(404);
  });

  test('schedule links on event pages lead to working talk and speaker pages', async ({ page, request }) => {
    await page.goto(city('varnaconf', '/archive/2024'));
    for (const href of await page.locator('#schedule h3 a, #schedule .who-one').evaluateAll((as) => as.map((a) => a.href))) {
      expect((await request.get(href)).status(), href).toBe(200);
    }
  });
});
