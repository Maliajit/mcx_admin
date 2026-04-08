<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LiveRatesApiTest extends TestCase
{
    public function test_it_returns_cached_fallback_when_upstream_data_exists_in_cache(): void
    {
        Cache::put('live_rates:v1:fallback', [
            'fetched_at' => now()->toIso8601String(),
            'served_at' => now()->toIso8601String(),
            'is_stale' => false,
            'source' => 'suvidhigold',
            'fallback_reason' => null,
            'items' => [
                [
                    'name' => 'GOLD',
                    'bid' => '100',
                    'ask' => '101',
                    'high' => '105',
                    'low' => '99',
                ],
            ],
        ]);

        config()->set('services.live_rates.url', 'http://127.0.0.1:1/unreachable');
        config()->set('services.live_rates.timeout_seconds', 1);

        $response = $this->getJson('/api/v1/live-rates');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.is_stale', true)
            ->assertJsonPath('data.source', 'suvidhigold')
            ->assertJsonPath('data.items.0.name', 'GOLD')
            ->assertJsonPath('error', null);
    }
}
