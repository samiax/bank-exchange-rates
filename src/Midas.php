<?php

namespace Ahmeti\BankExchangeRates;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Midas
{
    const KEY = 'midas';

    const NAME = 'Midas';

    const BASE_URL = 'https://www.getmidas.com';

    const DATA_URL = 'https://www.getmidas.com/canli-borsa';

    protected $items = [];

    public function get(): array
    {
        $client = new Client;
        $headers = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Pragma' => 'no-cache',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'none',
            'Sec-Fetch-User' => '?1',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers,
        ]);

        $crawler = new Crawler($res->getBody()->getContents());

        $crawler->filter('.stock-table-container .stock-table')
            ->filter('.table-body > .table-row')
            ->each(function ($row, $i) {
                $currCode = trim($row->filter('td')->eq(0)->text());
                $buy = $row->filter('td')->eq(2)->text();
                $sell = $row->filter('td')->eq(3)->text();
                $this->items[] = [
                    'key' => self::KEY,
                    'name' => self::NAME,
                    'symbol' => $currCode . '/TRY',
                    'buy' => Service::toFloat($buy),
                    'sell' => Service::toFloat($sell),
                    'time' => date('Y-m-d H:i:s'),
                    'description' => null,
                ];
            });

        return $this->items;
    }
}
