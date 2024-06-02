<?php

namespace Ahmeti\BankExchangeRates;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;

class BtcTurk
{
    const KEY = 'btcturk';
    const NAME = 'BTC Türk';
    const BASE_URL = 'https://www.btcturk.com';
    const DATA_URL = 'https://api.btcturk.com/api/v2/server/exchangeinfo';

    protected $items = [];

    public function get(): array
    {
        $now = new DateTime('now', new DateTimeZone('Europe/Istanbul'));

        $client = new Client();
        $headers = [
            'Accept' => '*/*',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Pragma' => 'no-cache',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest',
            'sec-ch-ua' => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"'
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers
        ]);

        $items = json_decode($res->getBody()->getContents(), true);

        foreach ($items['data']['symbols'] as $item) {
            if (in_array($item['name'], ['BTCTRY', 'ETHTRY', 'AVAXTRY'])) {
                $this->items[] = [
                    'key' => self::KEY,
                    'name' => self::NAME,
                    'symbol' => $item['numerator'] . '/TRY',
                    'buy' => $item['maximumLimitOrderPrice'],
                    'sell' => $item['maximumLimitOrderPrice'],
                    'time' => $now->format('Y-m-d H:i:s'),
                    'description' => null,
                ];
            }
        }

        return $this->items;
    }
}
