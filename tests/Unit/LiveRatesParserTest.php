<?php

namespace Tests\Unit;

use App\Services\LiveRates\LiveRatesParser;
use RuntimeException;
use Tests\TestCase;

class LiveRatesParserTest extends TestCase
{
    public function test_it_parses_valid_tab_delimited_payload(): void
    {
        $parser = new LiveRatesParser();

        $items = $parser->parse("1\tGOLD\t156604\t156642\t157580\t156266\n2\tSILVER\t257400\t257648\t262899\t257035");

        $this->assertCount(2, $items);
        $this->assertSame('GOLD', $items[0]['name']);
        $this->assertSame('156642', $items[0]['ask']);
        $this->assertSame('SILVER', $items[1]['name']);
    }

    public function test_it_rejects_invalid_payloads(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Parsed 0 valid rows');

        (new LiveRatesParser())->parse("bad-data\nstill-bad");
    }
}
