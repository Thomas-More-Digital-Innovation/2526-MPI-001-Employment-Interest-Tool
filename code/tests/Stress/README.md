# Stress Testing with PEST PHP Stressless

This directory contains stress tests for the MPI Employment Interest Tool using PEST PHP's Stressless plugin.

## Overview

Stress testing helps you understand how your application performs under load. The Stressless plugin makes it easy to:
- Test response times under various load conditions
- Identify performance bottlenecks
- Ensure your application can handle concurrent users
- Validate that your infrastructure is properly scaled

## Running Stress Tests

### Run all stress tests
```bash
./vendor/bin/pest tests/Stress
```

### Run a specific stress test
```bash
./vendor/bin/pest tests/Stress/HomepageStressTest.php
```

### Run specific test suites
```bash
./vendor/bin/pest --testsuite=Stress
```

## Test Files

### `HomepageStressTest.php`
Basic stress test for the homepage with reasonable thresholds:
- Tests the production domain: `https://mpi.stijnrombouts.be`
- Uses default settings (10s duration, 1 concurrent user)
- Checks for reasonable response times (median < 2s)
- Allows up to 10% failure rate

### `HomepageStressTestExamples.php`
Contains multiple stress test scenarios with different intensities:
- **Basic stress**: Default settings for quick validation
- **Moderate load**: 5 concurrent users for 20 seconds
- **Heavy load**: 10 concurrent users for 30 seconds (skipped by default)

The intensive tests are skipped by default to avoid overwhelming the server during regular test runs.

## Understanding Stressless Configuration

### Basic Usage
```php
$result = stress('https://mpi.stijnrombouts.be');
```

### Custom Configuration
```php
$result = stress('https://mpi.stijnrombouts.be')
    ->concurrency(5)  // Number of concurrent users
    ->duration(20);    // Test duration in seconds
```

## Metrics Available

The stress test result provides various metrics:

### Request Metrics
- `$result->requests()->count()` - Total number of requests made
- `$result->requests()->failed()->count()` - Number of failed requests

### Duration Metrics
- `$result->requests()->duration()->med()` - Median response time (milliseconds)
- `$result->requests()->duration()->p95()` - 95th percentile response time
- `$result->requests()->duration()->p99()` - 99th percentile response time
- `$result->requests()->duration()->min()` - Minimum response time
- `$result->requests()->duration()->max()` - Maximum response time

## Best Practices

1. **Start Small**: Begin with low concurrency and short durations
2. **Gradual Increase**: Incrementally increase load to find breaking points
3. **Monitor Server**: Watch server metrics (CPU, memory, network) during tests
4. **Schedule Wisely**: Run intensive tests during off-peak hours
5. **Set Realistic Thresholds**: Base expectations on your infrastructure capabilities

## Creating New Stress Tests

To add a new stress test:

1. Create a new file in `tests/Stress/`
2. Use the `stress()` function from Stressless
3. Configure concurrency and duration as needed
4. Add assertions for your expected performance metrics

Example:
```php
<?php

use function Pest\Stressless\stress;

test('api endpoint handles load', function () {
    $result = stress('https://mpi.stijnrombouts.be/api/endpoint')
        ->concurrency(3)
        ->duration(15);

    expect($result->requests()->duration()->med())->toBeLessThan(1000);
});
```

## Skipping Intensive Tests

Mark tests as skipped by default to prevent accidental heavy load:

```php
test('heavy load test', function () {
    // ... test code ...
})->skip('Run manually for intensive testing');
```

To run skipped tests:
```bash
./vendor/bin/pest --testsuite=Stress --exclude-group=default
```

Or remove the `->skip()` modifier temporarily.

## Troubleshooting

### Connection Failures
If you see high failure rates:
- Check if the server is accessible
- Verify SSL certificates are valid
- Ensure firewall rules allow the connections
- Check if rate limiting is enabled

### Slow Response Times
If response times are higher than expected:
- Monitor server resource usage
- Check database query performance
- Review application logs for errors
- Consider caching strategies

## Additional Resources

- [PEST PHP Documentation](https://pestphp.com)
- [Stressless Plugin Documentation](https://github.com/pestphp/pest-plugin-stressless)
