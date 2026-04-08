<?php

namespace App\Services\LiveRates;

use Carbon\CarbonInterface;

class LiveRatesResponseFormatter
{
    /**
     * @param array<int, array{name:string,bid:string,ask:string,high:string,low:string}> $items
     * @return array<string, mixed>
     */
    public function format(
        array $items,
        CarbonInterface $fetchedAt,
        CarbonInterface $servedAt,
        bool $isStale = false,
        ?string $error = null,
        ?string $fallbackReason = null,
    ): array {
        return [
            'fetched_at' => $fetchedAt->toIso8601String(),
            'served_at' => $servedAt->toIso8601String(),
            'is_stale' => $isStale,
            'source' => 'suvidhigold',
            'fallback_reason' => $fallbackReason,
            'items' => array_values(array_map(
                fn (array $item): array => [
                    'name' => $item['name'],
                    'bid' => $item['bid'],
                    'ask' => $item['ask'],
                    'high' => $item['high'],
                    'low' => $item['low'],
                ],
                $items,
            )),
            'upstream_error' => $error,
        ];
    }
}
