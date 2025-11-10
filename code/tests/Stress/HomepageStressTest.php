<?php

use function Pest\Stressless\stress;

test('homepage can handle stress', function () {
    $result = stress('https://mpi.stijnrombouts.be');

    // Display stress test results
    expect($result->requests()->count())->toBeGreaterThan(0);

    // Check median response time is reasonable (under 2 seconds)
    expect($result->requests()->duration()->med())->toBeLessThan(2000);

    // Allow some failures but keep them under 10%
    $totalRequests = $result->requests()->count();
    $failedRequests = $result->requests()->failed()->count();
    $failureRate = $totalRequests > 0 ? ($failedRequests / $totalRequests) * 100 : 0;

    expect($failureRate)->toBeLessThan(10);
});
