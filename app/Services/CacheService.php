<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    protected const DEFAULT_TTL = 3600; // 1 hour
    protected const CACHE_PREFIX = 'csc_';

    /**
     * Get cached data or execute callback and cache result
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $cacheKey = $this->getCacheKey($key);
        $ttl = $ttl ?? self::DEFAULT_TTL;

        try {
            return Cache::remember($cacheKey, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning('Cache operation failed', [
                'key' => $cacheKey,
                'error' => $e->getMessage()
            ]);

            // Fallback to executing callback directly
            return $callback();
        }
    }

    /**
     * Cache geographical data (countries, states, cities)
     */
    public function cacheGeographicalData(string $type, callable $callback, ?int $ttl = null): mixed
    {
        $key = "geographical_data_{$type}";
        return $this->remember($key, $callback, $ttl ?? 7200); // 2 hours for geo data
    }

    /**
     * Cache change request statistics
     */
    public function cacheChangeRequestStats(callable $callback): mixed
    {
        return $this->remember('change_request_stats', $callback, 1800); // 30 minutes
    }

    /**
     * Cache user-specific data
     */
    public function cacheUserData(int $userId, string $dataType, callable $callback): mixed
    {
        $key = "user_{$userId}_{$dataType}";
        return $this->remember($key, $callback, 1800); // 30 minutes
    }

    /**
     * Invalidate cache by pattern
     */
    public function forget(string $key): bool
    {
        $cacheKey = $this->getCacheKey($key);

        try {
            return Cache::forget($cacheKey);
        } catch (\Exception $e) {
            Log::warning('Cache forget failed', [
                'key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Invalidate geographical data cache
     */
    public function forgetGeographicalData(string $type): bool
    {
        return $this->forget("geographical_data_{$type}");
    }

    /**
     * Invalidate change request related caches
     */
    public function forgetChangeRequestCaches(): void
    {
        $this->forget('change_request_stats');

        // Could add more specific cache invalidation here
        $patterns = [
            'change_request_*',
            'user_*_change_requests',
            'admin_dashboard_*'
        ];

        foreach ($patterns as $pattern) {
            $this->forgetByPattern($pattern);
        }
    }

    /**
     * Clear all application caches
     */
    public function clearAll(): bool
    {
        try {
            return Cache::flush();
        } catch (\Exception $e) {
            Log::error('Cache clear all failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        try {
            // This would depend on your cache driver
            // For Redis, you could get more detailed stats
            return [
                'driver' => config('cache.default'),
                'prefix' => self::CACHE_PREFIX,
                'default_ttl' => self::DEFAULT_TTL,
            ];
        } catch (\Exception $e) {
            Log::warning('Cache stats retrieval failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Generate cache key with prefix
     */
    protected function getCacheKey(string $key): string
    {
        return self::CACHE_PREFIX . $key;
    }

    /**
     * Forget cache entries by pattern (Redis specific)
     */
    protected function forgetByPattern(string $pattern): void
    {
        try {
            if (config('cache.default') === 'redis') {
                $redis = Cache::getRedis();
                $keys = $redis->keys(self::CACHE_PREFIX . $pattern);

                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Pattern cache forget failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
        }
    }
}
