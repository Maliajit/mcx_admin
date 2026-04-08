<?php

namespace App\Services\LiveRates;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Throwable;

class LiveRatesService
{
    private const CACHE_KEY_LATEST = 'live_rates:v1:latest';
    private const CACHE_KEY_FALLBACK = 'live_rates:v1:fallback';

    public function __construct(
        private readonly UpstreamLiveRatesClient $client,
        private readonly LiveRatesParser $parser,
        private readonly LiveRatesResponseFormatter $formatter,
        private readonly CacheRepository $cache,
    ) {
    }

    /**
     * @return array{status:int,success:bool,data:array<string,mixed>,error:?string}
     */
    public function getLiveRates(): array
    {
        try {
            $payload = $this->client->fetch();
            $items = $this->parser->parse($payload);
            $fetchedAt = now();
            $servedAt = now();

            $body = $this->formatter->format($items, $fetchedAt, $servedAt);
            $this->cache->put(self::CACHE_KEY_LATEST, $body, now()->addSeconds(
                (int) config('services.live_rates.cache_ttl_seconds', 3)
            ));
            $this->cache->forever(self::CACHE_KEY_FALLBACK, $body);

            Log::info('Live rates refreshed from upstream.', [
                'source' => 'suvidhigold',
                'items_count' => count($items),
                'fetched_at' => $fetchedAt->toIso8601String(),
            ]);

            return [
                'status' => 200,
                'success' => true,
                'data' => $body,
                'error' => null,
            ];
        } catch (Throwable $exception) {
            report($exception);
            Log::warning('Live rates refresh failed.', [
                'source' => 'suvidhigold',
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            $cached = $this->cache->get(self::CACHE_KEY_LATEST)
                ?? $this->cache->get(self::CACHE_KEY_FALLBACK);

            if (is_array($cached)) {
                Log::notice('Serving stale live rates from cache fallback.', [
                    'source' => 'suvidhigold',
                    'cached_timestamp' => $cached['fetched_at'] ?? null,
                ]);

                $cached = $this->formatter->format(
                    $cached['items'] ?? [],
                    Carbon::parse($cached['fetched_at'] ?? now()->toIso8601String()),
                    Carbon::now(),
                    true,
                    $exception->getMessage(),
                    'Live rates temporarily unavailable. Serving cached data.',
                );

                return [
                    'status' => 200,
                    'success' => true,
                    'data' => $cached,
                    'error' => null,
                ];
            }

            return [
                'status' => 503,
                'success' => false,
                'data' => [
                    'fetched_at' => Carbon::now()->toIso8601String(),
                    'served_at' => Carbon::now()->toIso8601String(),
                    'is_stale' => false,
                    'source' => 'suvidhigold',
                    'fallback_reason' => $exception->getMessage(),
                    'items' => [],
                ],
                'error' => 'Live rates unavailable and no cached data exists yet.',
            ];
        }
    }
}
