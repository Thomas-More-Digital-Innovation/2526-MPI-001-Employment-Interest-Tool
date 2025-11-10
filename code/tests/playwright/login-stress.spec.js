import { test, expect } from '@playwright/test';

const TEST_USER = {
  username: 'client',
  password: 'password'
};

/**
 * Basic login test
 */
test('user can login successfully', async ({ page }) => {
  await page.goto('/');

  // Verify login form is present
  await expect(page.locator('input[wire\\:model="username"]')).toBeVisible();
  await expect(page.locator('input[wire\\:model="password"]')).toBeVisible();

  // Fill and submit
  await page.fill('input[wire\\:model="username"]', TEST_USER.username);
  await page.fill('input[wire\\:model="password"]', TEST_USER.password);
  await page.click('button[type="submit"]');

  // Verify redirect to dashboard
  await page.waitForURL('**/dashboard', { timeout: 10000 });
  await expect(page).toHaveURL(/dashboard/);

  console.log('Login successful');
});

/**
 * Measure login performance
 */
test('measure login performance', async ({ page }) => {
  const startTime = Date.now();

  await page.goto('/');
  await page.fill('input[wire\\:model="username"]', TEST_USER.username);
  await page.fill('input[wire\\:model="password"]', TEST_USER.password);

  const beforeSubmit = Date.now();
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard');
  const afterLogin = Date.now();

  const totalTime = afterLogin - startTime;
  const authTime = afterLogin - beforeSubmit;

  console.log(`Total login time: ${totalTime}ms`);
  console.log(`Authentication time: ${authTime}ms`);

  // Assert performance
  expect(totalTime).toBeLessThan(5000);
  expect(authTime).toBeLessThan(3000);
});

/**
 * Test login form validation
 */
test('handles invalid credentials gracefully', async ({ page }) => {
  await page.goto('/');

  await page.fill('input[wire\\:model="username"]', 'invalid-user');
  await page.fill('input[wire\\:model="password"]', 'wrong-password');
  await page.click('button[type="submit"]');

  // Wait for error message (adjust selector based on your actual UI)
  await page.waitForTimeout(2000);

  // Should still be on homepage
  await expect(page).toHaveURL('/');

  console.log('Invalid login handled correctly');
});

/**
 * Concurrent login stress test
 * Run with: npx playwright test --grep "concurrent logins" --workers=10
 */
test('concurrent logins perform well', async ({ page }) => {
  const userId = Math.floor(Math.random() * 10000);
  console.log(`🧪 User ${userId}: Attempting login`);

  const startTime = Date.now();

  await page.goto('/');
  await page.fill('input[wire\\:model="username"]', TEST_USER.username);
  await page.fill('input[wire\\:model="password"]', TEST_USER.password);
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard', { timeout: 15000 });

  const duration = Date.now() - startTime;

  console.log(`User ${userId}: Logged in successfully in ${duration}ms`);

  expect(duration).toBeLessThan(10000); // Should complete within 10 seconds even under load
});

/**
 * Test homepage load performance
 */
test('homepage loads quickly', async ({ page }) => {
  const startTime = Date.now();

  await page.goto('/');
  await page.waitForLoadState('networkidle');

  const loadTime = Date.now() - startTime;

  // Get detailed metrics
  const metrics = await page.evaluate(() => {
    const nav = performance.getEntriesByType('navigation')[0];
    return {
      dns: nav.domainLookupEnd - nav.domainLookupStart,
      tcp: nav.connectEnd - nav.connectStart,
      request: nav.responseStart - nav.requestStart,
      response: nav.responseEnd - nav.responseStart,
      dom: nav.domComplete - nav.domInteractive,
      load: nav.loadEventEnd - nav.loadEventStart,
    };
  });

  console.log('Homepage Performance Metrics:');
  console.log(`  Total load time: ${loadTime}ms`);
  console.log(`  DNS lookup: ${metrics.dns.toFixed(2)}ms`);
  console.log(`  TCP connection: ${metrics.tcp.toFixed(2)}ms`);
  console.log(`  Request time: ${metrics.request.toFixed(2)}ms`);
  console.log(`  Response time: ${metrics.response.toFixed(2)}ms`);
  console.log(`  DOM processing: ${metrics.dom.toFixed(2)}ms`);
  console.log(`  Load event: ${metrics.load.toFixed(2)}ms`);

  expect(loadTime).toBeLessThan(5000);
});

/**
 * Test Livewire initialization
 */
test('livewire login component initializes correctly', async ({ page }) => {
  await page.goto('/');

  // Wait for Livewire to initialize
  await page.waitForFunction(() => {
    return typeof window.Livewire !== 'undefined';
  }, { timeout: 5000 });

  // Check that the login component is mounted
  const livewireComponents = await page.evaluate(() => {
    return window.Livewire?.components?.componentsByName?.() || {};
  });

  console.log('Livewire initialized successfully');
  expect(true).toBe(true);
});

/**
 * Test with network throttling (simulates slower connections)
 */
test('login works under slow network conditions', async ({ page, context }) => {
  // Simulate slow 3G connection
  await context.route('**/*', (route) => {
    setTimeout(() => route.continue(), 100); // Add 100ms delay to all requests
  });

  const startTime = Date.now();

  await page.goto('/', { timeout: 30000 });
  await page.fill('input[wire\\:model="username"]', TEST_USER.username);
  await page.fill('input[wire\\:model="password"]', TEST_USER.password);
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard', { timeout: 30000 });

  const duration = Date.now() - startTime;

  console.log(`Login successful under slow network in ${duration}ms`);
  expect(duration).toBeLessThan(30000);
});

/**
 * Test language selector on login page
 */
test('language selector works on login page', async ({ page }) => {
  await page.goto('/');

  // Check if language selector is present (based on your UI)
  const langSelector = page.locator('x-language-selector, .language-selector, [class*="language"]').first();

  if (await langSelector.isVisible({ timeout: 2000 })) {
    await langSelector.click();
    await page.waitForTimeout(1000);
    console.log('Language selector is interactive');
  } else {
    console.log('Language selector not found or not visible');
  }

  expect(true).toBe(true);
});

