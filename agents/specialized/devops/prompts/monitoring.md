# Monitoring Configuration - DevOps

## Monitoring Stack for Condominios-Vzla

### 1. Laravel Telescope (Development/Staging)
```bash
# Installation
php artisan telescope:install
php artisan migrate

# Access: Only for authorized users
# Config: config/telescope.php

# Prune old entries (add to scheduler)
$schedule->command('telescope:prune --hours=48')->daily();
```

**What Telescope monitors:**
- All incoming requests
- All database queries (with EXPLAIN)
- All queued jobs
- All exceptions
- All log entries
- All cache operations
- All mail sent
- All notifications

### 2. Application Logging
```php
// config/logging.php
'channels' => [
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
        'days' => 14,
    ],
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'telescope'],
    ],
],
```

### 3. Key Metrics to Monitor
```yaml
application:
  - response_time_p95: "< 500ms"
  - response_time_p99: "< 1000ms"
  - error_rate: "< 1%"
  - throughput: "requests per minute"
  - queue_size: "pending jobs count"

database:
  - slow_queries: "< 2s"
  - connection_pool_usage: "< 80%"
  - replication_lag: "< 1s"
  - disk_usage: "< 80%"

infrastructure:
  - cpu_usage: "< 70%"
  - memory_usage: "< 80%"
  - disk_usage: "< 85%"
  - network_io: "bytes in/out"
```

### 4. Health Check Endpoint
```php
// routes/api.php
Route::get('/health', function () {
    $checks = [
        'database' => DB::connection()->getPdo() ? 'ok' : 'error',
        'cache' => Cache::get('health_check') === null ? 'ok' : 'error',
        'storage' => is_writable(storage_path()) ? 'ok' : 'error',
    ];

    $status = in_array('error', $checks) ? 503 : 200;

    return response()->json([
        'status' => $status === 200 ? 'healthy' : 'unhealthy',
        'checks' => $checks,
        'timestamp' => now()->toISOString(),
    ], $status);
});
```

### 5. Alert Rules
```yaml
alerts:
  - name: "high_error_rate"
    condition: "error_rate > 5% over 5 minutes"
    severity: "critical"
    notify: ["slack", "email"]

  - name: "slow_response"
    condition: "p95_response_time > 2000ms over 10 minutes"
    severity: "warning"
    notify: ["slack"]

  - name: "database_connection_failed"
    condition: "database check fails"
    severity: "critical"
    notify: ["slack", "email", "sms"]

  - name: "disk_space_low"
    condition: "disk_usage > 90%"
    severity: "critical"
    notify: ["slack", "email"]

  - name: "queue_backlog"
    condition: "queue_size > 1000 for 15 minutes"
    severity: "warning"
    notify: ["slack"]
```

### 6. Performance Baseline
```
Page Load Times:
- Dashboard: < 2s
- CRUD Index: < 1s
- CRUD Form: < 500ms
- API Endpoints: < 300ms

Database:
- Simple queries: < 50ms
- Complex queries: < 200ms
- DataTables: < 500ms

Queue:
- Email delivery: < 5s
- PDF generation: < 10s
- Report generation: < 30s
```

### 7. Log Analysis Commands
```bash
# Find errors in last hour
grep -E "\[(error|critical)\]" storage/logs/laravel.log | tail -100

# Find slow queries from Telescope
php artisan tinker
>>> Telescope::entries(\Laravel\Telescope\EntryType::QUERY)->where('duration', '>', 1000)->get()

# Check queue status
php artisan queue:monitor default

# Monitor real-time logs
tail -f storage/logs/laravel.log
```
