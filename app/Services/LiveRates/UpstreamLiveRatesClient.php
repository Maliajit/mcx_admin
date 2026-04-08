<?php

namespace App\Services\LiveRates;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;

class UpstreamLiveRatesClient
{
    public function __construct(
        private readonly HttpFactory $http,
    ) {
    }

    /**
     * @throws RequestException
     */
    public function fetch(): string
    {
        $response = $this->http
            ->timeout((int) config('services.live_rates.timeout_seconds', 3))
            ->connectTimeout((int) config('services.live_rates.connect_timeout_seconds', 2))
            ->accept('text/plain')
            ->get(config('services.live_rates.url'), [
                '_' => now()->timestamp,
            ]);

        $response->throw();

        return trim((string) $response->body());
    }
}
