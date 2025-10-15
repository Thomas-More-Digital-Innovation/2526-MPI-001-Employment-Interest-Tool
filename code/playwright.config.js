import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright Configuration for MPI Employment Interest Tool
 *
 * See https://playwright.dev/docs/test-configuration
 */
export default defineConfig({
  // Test directory
  testDir: './tests/playwright',

  // Maximum time one test can run (10 minutes for long stress tests)
  timeout: 600 * 1000,

  // Run tests in files in parallel
  fullyParallel: true,

  // Fail the build on CI if you accidentally left test.only in the source code
  forbidOnly: !!process.env.CI,

  // Retry on CI only
  retries: process.env.CI ? 2 : 0,

  // Number of parallel workers (adjust based on your server capacity)
  // For stress testing, increase this number
  workers: process.env.CI ? 2 : 5,

  // Reporter to use
  reporter: [
    ['html'],
    ['list'],
    ['json', { outputFile: 'test-results.json' }]
  ],

  // Shared settings for all projects
  use: {
    // Base URL to use in actions like `await page.goto('/')`
    baseURL: 'https://mpi.stijnrombouts.be',

    // Collect trace when retrying the failed test
    trace: 'on-first-retry',

    // Screenshot on failure
    screenshot: 'only-on-failure',

    // Record video on failure
    video: 'retain-on-failure',

    // Maximum time each action can take (30 seconds for slow actions)
    actionTimeout: 30 * 1000,

    // Emulate viewport
    viewport: { width: 1280, height: 720 },

    // Accept downloads
    acceptDownloads: true,

    // Locale
    locale: 'nl-BE',

    // Timezone
    timezoneId: 'Europe/Brussels',
  },

  // Configure projects for different browsers
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },

    // Uncomment to test on other browsers
    // {
    //   name: 'firefox',
    //   use: { ...devices['Desktop Firefox'] },
    // },

    // {
    //   name: 'webkit',
    //   use: { ...devices['Desktop Safari'] },
    // },

    // Test against mobile viewports
    // {
    //   name: 'Mobile Chrome',
    //   use: { ...devices['Pixel 5'] },
    // },
    // {
    //   name: 'Mobile Safari',
    //   use: { ...devices['iPhone 12'] },
    // },

    // Test against branded browsers
    // {
    //   name: 'Microsoft Edge',
    //   use: { ...devices['Desktop Edge'], channel: 'msedge' },
    // },
    // {
    //   name: 'Google Chrome',
    //   use: { ...devices['Desktop Chrome'], channel: 'chrome' },
    // },
  ],

  // Run your local dev server before starting the tests
  // Uncomment if you want to test against local dev server
  // webServer: {
  //   command: 'php artisan serve',
  //   url: 'http://127.0.0.1:8000',
  //   reuseExistingServer: !process.env.CI,
  // },
});
