# Stress Testing Setup Complete! 🚀

Stress testing has been successfully added to your Laravel project using PEST PHP and the Stressless plugin.

## What Was Installed

- ✅ `pestphp/pest-plugin-stressless` v4.0 - Added to composer.json as a dev dependency
- ✅ Created `tests/Stress/` directory for organizing stress tests
- ✅ Added "Stress" test suite to `phpunit.xml`

## Test Files Created

### 1. `tests/Stress/HomepageStressTest.php`
Basic stress test that's ready to run:
- Tests: `https://mpi.stijnrombouts.be`
- Runs by default (not skipped)
- Checks median response time < 2 seconds
- Allows up to 10% failure rate

### 2. `tests/Stress/HomepageStressTestExamples.php`
Additional examples with varying intensity levels:
- Basic stress test
- Moderate load (5 concurrent users, 20 seconds)
- Heavy load (10 concurrent users, 30 seconds)
- Heavy tests are skipped by default

### 3. `tests/Stress/AdvancedStressTestExamples.php`
Advanced examples for:
- Testing login pages
- Testing static assets
- API endpoints with authentication
- POST requests
- All skipped by default (templates for you to customize)

### 4. `tests/Stress/README.md`
Complete documentation on how to use and extend the stress tests.

## Quick Start

### Run the basic stress test:
```bash
cd /home/stijn/Documents/git/2526-MPI-001-Employment-Interest-Tool/code
./vendor/bin/pest tests/Stress/HomepageStressTest.php
```

### Run all stress tests:
```bash
./vendor/bin/pest --testsuite=Stress
```

### Run all tests including unit and feature tests:
```bash
./vendor/bin/pest
```

## Next Steps

1. **Run the basic test** to verify everything works
2. **Review the examples** in `HomepageStressTestExamples.php`
3. **Customize thresholds** based on your performance requirements
4. **Add more tests** for your critical endpoints
5. **Monitor your server** during stress tests

## Customization Examples

### Adjust concurrency and duration:
```php
$result = stress('https://mpi.stijnrombouts.be')
    ->concurrency(5)  // 5 simultaneous users
    ->duration(20);    // Run for 20 seconds
```

### Test with authentication:
```php
$result = stress('https://mpi.stijnrombouts.be/api/endpoint')
    ->withHeaders([
        'Authorization' => 'Bearer your-token',
    ]);
```

### Test POST requests:
```php
$result = stress('https://mpi.stijnrombouts.be/api/endpoint')
    ->post(['key' => 'value']);
```

## Available Metrics

- `$result->requests()->count()` - Total requests
- `$result->requests()->failed()->count()` - Failed requests
- `$result->requests()->duration()->med()` - Median response time
- `$result->requests()->duration()->p95()` - 95th percentile
- `$result->requests()->duration()->p99()` - 99th percentile
- `$result->requests()->duration()->min()` - Fastest response
- `$result->requests()->duration()->max()` - Slowest response

## Important Notes

⚠️ **Be Careful**: Stress testing can impact your production server. Start with low values and monitor your infrastructure.

⚠️ **Skipped Tests**: The more intensive tests are skipped by default. Remove `->skip()` when you're ready to run them.

⚠️ **Rate Limiting**: If your site has rate limiting, you may see failed requests. Adjust expectations accordingly.

## Documentation

For complete documentation, see `tests/Stress/README.md`

---

Happy stress testing! 🔥
