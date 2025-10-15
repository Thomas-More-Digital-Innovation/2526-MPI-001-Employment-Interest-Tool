<?php

use function Pest\Stressless\stress;

/**
 * Basic stress test for the homepage
 * Uses default settings: 10 seconds duration, 1 concurrent user
 */
test('homepage loads successfully under basic stress', function () {
    $result = stress('https://mpi.stijnrombouts.be');

    expect($result->requests()->count())->toBeGreaterThan(0);
    expect($result->requests()->duration()->med())->toBeLessThan(2000); // median < 2s
});

/**
 * More intensive stress test with multiple concurrent users
 * This simulates realistic traffic patterns
 */
test('homepage handles moderate concurrent load', function () {
    $result = stress('https://mpi.stijnrombouts.be')
        ->concurrency(5)  // 5 concurrent users
        ->duration(20);    // for 20 seconds

    $totalRequests = $result->requests()->count();
    $failedRequests = $result->requests()->failed()->count();
    $failureRate = $totalRequests > 0 ? ($failedRequests / $totalRequests) * 100 : 0;

    // Expect reasonable performance metrics
    expect($totalRequests)->toBeGreaterThan(50); // Should handle at least 50 requests
    expect($result->requests()->duration()->med())->toBeLessThan(3000); // median < 3s
    expect($result->requests()->duration()->p95())->toBeLessThan(5000); // 95th percentile < 5s
    expect($failureRate)->toBeLessThan(5); // Less than 5% failure rate
})->skip('Run manually for intensive testing');

/**
 * Heavy load stress test
 * This tests the maximum capacity of the application
 */
test('homepage survives heavy load', function () {
    $result = stress('https://mpi.stijnrombouts.be')
        ->concurrency(10) // 10 concurrent users
        ->duration(30);    // for 30 seconds

    $totalRequests = $result->requests()->count();
    $failedRequests = $result->requests()->failed()->count();
    $successfulRequests = $totalRequests - $failedRequests;

    // We expect some failures under heavy load, but most should succeed
    expect($successfulRequests)->toBeGreaterThan($totalRequests * 0.8); // 80% success rate minimum
    expect($result->requests()->duration()->med())->toBeLessThan(5000); // median < 5s
})->skip('Run manually for intensive testing');
