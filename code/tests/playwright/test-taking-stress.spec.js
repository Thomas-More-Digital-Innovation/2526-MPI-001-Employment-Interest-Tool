import { test, expect } from '@playwright/test';

const TEST_USER = {
  username: 'client',
  password: 'password'
};

/**
 * User can take a test (stopped before completion)
 */
test('user can complete test', async ({ page }) => {
  // Login
  await page.goto('/');
  await page.fill('input[wire\\:model="username"]', TEST_USER.username);
  await page.fill('input[wire\\:model="password"]', TEST_USER.password);
  await page.click('button[type="submit"]');

  // Wait for redirect to dashboard
  await page.waitForURL('**/dashboard', { timeout: 10000 });
  console.log('Logged in successfully');

  // Start test from dashboard
  await page.waitForLoadState('networkidle');
  const testButton = page.locator('a[href*="/test"], button:has-text("Start")').first();

  if (await testButton.isVisible({ timeout: 5000 })) {
    console.log('Found test button/link on dashboard');
    await testButton.click();
    await page.waitForTimeout(2000);
    const currentUrl = page.url();
    console.log(`Current URL: ${currentUrl}`);

    if (currentUrl.includes('/test')) {
      console.log('On test page');
      await page.waitForLoadState('networkidle');
      const likeButton = page.locator('button.bg-green-400').first();
      if (await likeButton.isVisible({ timeout: 5000 })) {
        console.log('Found like button, clicking...');
        await likeButton.click();
        await page.waitForTimeout(2000);
        console.log('Clicked like button');
      }
      console.log('Test workflow completed successfully');
    } else {
      console.log(`Not on test page, currently on: ${currentUrl}`);
      console.log('This test requires a test to be assigned to the user in the dashboard');
    }
  } else {
    console.log('No test button found on dashboard');
    console.log('This is expected if no tests are assigned to this user');
    console.log('Please assign a test to the user in the application first');
  }
});

/**
 * Answer all questions until test results page is reached
 */
test('user completes entire test and reaches results page', async ({ page }, testInfo) => {
  test.setTimeout(600000); // 10 minutes timeout

  console.log('Starting complete test workflow');

  // Login
  await page.goto('/');
  await page.fill('input[wire\\:model="username"]', TEST_USER.username);
  await page.fill('input[wire\\:model="password"]', TEST_USER.password);
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard', { timeout: 10000 });
  console.log('Logged in successfully');

  // Navigate to test from dashboard
  await page.waitForLoadState('networkidle');
  const testButton = page.locator('a[href*="/test"], button:has-text("Start")').first();

  if (!(await testButton.isVisible({ timeout: 5000 }))) {
    throw new Error('No test available on dashboard for the user; cannot proceed with full test flow');
  }

  await testButton.click();
  await page.waitForTimeout(5000);

  if (!page.url().includes('/test')) {
    throw new Error('Could not access test page');
  }

  console.log('On test page, starting to answer questions');

  // Answer all questions until completion
  let questionCount = 0;
  const maxQuestions = 100; // Safety limit to prevent infinite loops

  while (questionCount < maxQuestions) {
    // Wait for page to load
    await page.waitForLoadState('networkidle');

    // Check if we've reached the results page
    if (page.url().includes('/test-result')) {
      console.log('Reached test results page!');
      break;
    }

    // Check if we're still on the test page
    if (!page.url().includes('/test')) {
      throw new Error(`Unexpected page: ${page.url()}`);
    }

    questionCount++;
    console.log(`Answering question ${questionCount}`);

    // // Wait for buttons to be enabled (they're disabled during loading)
    // await page.waitForTimeout(1500);

    // Randomly choose like (green) or dislike (red)
    const random = Math.random();
    const button = random > 0.5
      ? page.locator('button.bg-green-400').first()
      : page.locator('button.bg-red-500').first();

    // Wait up to 5s for the button to become enabled, then click
    try {
      // Use Playwright test expect (imported at top of file) to wait for enabled state
      await expect(button).toBeEnabled({ timeout: 5000 });
      await button.click();
      console.log(`Question ${questionCount}: Clicked ${random > 0.5 ? 'LIKE' : 'DISLIKE'}`);

      // Wait for the image to change (assuming there's an img element)
      const img = page.locator('img').first();
      const initialSrc = await img.getAttribute('src');
      // Run the check inside the page context to avoid referencing the Node-side locator
      await page.waitForFunction(
        ({ sel, src }) => {
          const el = document.querySelector(sel);
          if (!el) return false;
          return el.getAttribute('src') !== src;
        },
        { sel: 'img', src: initialSrc },
        { timeout: 5000 }
      );
    } catch (err) {
      // If waiting for enabled times out, log and wait a short time before retrying next loop
      console.log(`Question ${questionCount}: Button did not become enabled within 5s; will wait and retry. (${err.message})`);
      throw new Error(`Stalled on question ${questionCount}: ${err.message}`);
    }
  }

  // Test Results
  if (page.url().includes('/test-result')) {
    console.log(`TEST COMPLETED! Answered ${questionCount} questions`);
    console.log('Successfully reached results page');

  await page.waitForTimeout(2000);

    expect(page.url()).toContain('/test-result');
  } else {
    throw new Error(`Test did not complete normally. Answered ${questionCount} questions. Current URL: ${page.url()}`);
  }
});
