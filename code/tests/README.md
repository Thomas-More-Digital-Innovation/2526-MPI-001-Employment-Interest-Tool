## Playwright

#### Run all Playwright tests
`npx playwright test`


#### Run in headed mode (see the browser)
`npx playwright test --headed`

#### Run with UI mode (interactive)
`npx playwright test --ui`

#### Run the complete test with 10 workers
`npx playwright test tests/playwright/test-taking-stress.spec.js --workers=10 --repeat-each=10 --grep="user completes entire test"`

#### View test results
`npx playwright show-report`
