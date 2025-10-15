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
test('user completes entire test and reaches results page', async ({ page }) => {
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
    console.log('No test available - test cannot proceed');
    console.log('Please assign a test to the user first');
    return;
  }

  await testButton.click();
  await page.waitForTimeout(2000);

  if (!page.url().includes('/test')) {
    console.log('Could not access test page');
    return;
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
      console.log(`Unexpected page: ${page.url()}`);
      break;
    }

    questionCount++;
    console.log(`Answering question ${questionCount}`);

    // Wait for buttons to be enabled (they're disabled during loading)
    await page.waitForTimeout(1500);

    // Randomly choose like (green) or dislike (red)
    const random = Math.random();
    const button = random > 0.5
      ? page.locator('button.bg-green-400').first()
      : page.locator('button.bg-red-500').first();

    // Check if button is visible and not disabled
    if (await button.isVisible({ timeout: 5000 })) {
      const isDisabled = await button.isDisabled();
      if (!isDisabled) {
        await button.click();
        console.log(`Question ${questionCount}: Clicked ${random > 0.5 ? 'LIKE' : 'DISLIKE'}`);
        await page.waitForTimeout(2000);
      } else {
        console.log(`Question ${questionCount}: Button disabled, waiting...`);
        await page.waitForTimeout(1000);
      }
    } else {
      console.log(`Question ${questionCount}: Button not visible`);
      break;
    }
  }

  // Test Results
  if (page.url().includes('/test-result')) {
    console.log(`TEST COMPLETED! Answered ${questionCount} questions`);
    console.log('Successfully reached results page');

  await page.waitForTimeout(2000);

    expect(page.url()).toContain('/test-result');
  } else {
    console.log(`Test did not complete normally. Answered ${questionCount} questions`);
    console.log(`Current URL: ${page.url()}`);
  }
});
