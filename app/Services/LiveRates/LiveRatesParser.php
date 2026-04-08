<?php

namespace App\Services\LiveRates;

use RuntimeException;

class LiveRatesParser
{
    /**
     * @return array<int, array{name:string,bid:string,ask:string,high:string,low:string}>
     */
    public function parse(string $payload): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($payload)) ?: [];
        $items = [];
        $invalidLineCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode("\t", $line));

            if (count($parts) < 6) {
                $invalidLineCount++;
                continue;
            }

            [$symbol, $name, $bid, $ask, $high, $low] = array_slice($parts, 0, 6);

            if ($symbol === '' || $name === '' || ! $this->looksNumeric($bid, $ask, $high, $low)) {
                $invalidLineCount++;
                continue;
            }

            $items[] = [
                'name' => $name,
                'bid' => $bid,
                'ask' => $ask,
                'high' => $high,
                'low' => $low,
            ];
        }

        if ($items === []) {
            throw new RuntimeException(sprintf(
                'Live rates payload was empty or invalid. Parsed 0 valid rows and rejected %d rows.',
                $invalidLineCount
            ));
        }

        return $items;
    }

    private function looksNumeric(string ...$values): bool
    {
        foreach ($values as $value) {
            if (! is_numeric(str_replace(',', '', $value))) {
                return false;
            }
        }

        return true;
    }
}
