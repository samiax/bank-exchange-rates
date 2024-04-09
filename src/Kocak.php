<?php

namespace Ahmeti\BankExchangeRates;

use DateTime;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Kocak
{
    const KEY = 'kocak';
    const NAME = 'Kocak';
    const BASE_URL = 'http://www.kocakkur.com';
    const DATA_URL = 'http://www.kocakkur.com';
    const REPLACES = [
        '24 AYAR GR ALTIN' => 'XAU-24AYAR/TRY',
        '22 AYAR BILEZIK' => 'XAU-22AYAR/TRY',
        'CUMHURİYET (ATA)' => 'XAU-ATA/TRY',
        'ESKİ ÇEYREK' => 'XAU-CEYREK/TRY',
        'ESKİ YARIM' => 'XAU-YARIM/TRY',
    ];

    protected $items = [];

    public function get(): array
    {
        $client = new Client();
        $headers = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Connection' => 'keep-alive',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers
        ]);

        $crawler = new Crawler($res->getBody()->getContents());

        $crawler->filter('#exchangeTableContent .area-value')
            ->each(function ($row, $i) {
                $currCode = $row->filter('.first .text-ellipsis')->eq(0)->text();
                $buy = $row->filter('.buying .text-ellipsis')->eq(0)->text();
                $sell = $row->filter('.selling .text-ellipsis')->eq(0)->text();

                if (!empty(self::REPLACES[$currCode])) {
                    $this->items[] = [
                        'key' => self::KEY,
                        'name' => self::NAME,
                        'symbol' => Service::replace(self::REPLACES, $currCode),
                        'buy' => Service::toFloat($buy),
                        'sell' => Service::toFloat($sell),
                        'time' => date('Y-m-d H:i:s'),
                        'description' => null,
                    ];
                }
            });

        return $this->items;
    }
}
