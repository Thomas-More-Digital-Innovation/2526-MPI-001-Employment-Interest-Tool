<?php

use function Pest\Stressless\stress;

/**
 * These tests are examples for testing various endpoints on the production site.
 * Uncomment and customize as needed for your specific use cases.
 */

/**
 * Test the login page under load
 */
test('login page handles stress', function () {
    $result = stress('https://mpi.stijnrombouts.be/login')
        ->concurrency(3)
        ->duration(15);

    expect($result->requests()->count())->toBeGreaterThan(0);
    expect($result->requests()->duration()->med())->toBeLessThan(2000);
    
    $failureRate = ($result->requests()->failed()->count() / $result->requests()->count()) * 100;
    expect($failureRate)->toBeLessThan(10);
})->skip('Run manually when testing authentication');

/**
 * Test static assets (like images, CSS, JS) under load
 */
test('static assets load quickly under stress', function () {
    $result = stress('https://mpi.stijnrombouts.be/LogoMPI.svg')
        ->concurrency(5)
        ->duration(10);

    // Static assets should be very fast
    expect($result->requests()->duration()->med())->toBeLessThan(500); // median < 500ms
    expect($result->requests()->duration()->p95())->toBeLessThan(1000); // 95th percentile < 1s
    
    // Static assets should rarely fail
    $failureRate = ($result->requests()->failed()->count() / $result->requests()->count()) * 100;
    expect($failureRate)->toBeLessThan(1);
})->skip('Run manually for asset testing');

/**
 * Stress test with custom headers (e.g., for testing with authentication tokens)
 */
test('api endpoint with authentication', function () {
    // Note: You'll need to replace 'your-token-here' with a real token
    $result = stress('https://mpi.stijnrombouts.be/api/some-endpoint')
        ->concurrency(2)
        ->duration(10)
        ->withHeaders([
            'Authorization' => 'Bearer your-token-here',
            'Accept' => 'application/json',
        ]);

    expect($result->requests()->count())->toBeGreaterThan(0);
    expect($result->requests()->duration()->med())->toBeLessThan(1500);
})->skip('Configure with real authentication token before running');

/**
 * POST request stress test example
 */
test('form submission handles load', function () {
    $result = stress('https://mpi.stijnrombouts.be/api/endpoint')
        ->post([
            'field1' => 'value1',
            'field2' => 'value2',
        ])
        ->concurrency(2)
        ->duration(10);

    expect($result->requests()->count())->toBeGreaterThan(0);
    
    $failureRate = ($result->requests()->failed()->count() / $result->requests()->count()) * 100;
    expect($failureRate)->toBeLessThan(10);
})->skip('Configure with valid POST data before running');
