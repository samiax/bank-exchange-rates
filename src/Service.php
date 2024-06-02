<?php

namespace Ahmeti\BankExchangeRates;

use DateTimeZone;

class Service
{
    protected $rates = [];

    protected function merge(array $items): void
    {
        foreach ($items as $item) {
            if (!array_key_exists($item['symbol'], $this->rates)) {
                $this->rates[$item['symbol']] = [];
            }

            $this->rates[$item['symbol']][$item['key']] = $item;
        }
    }

    public static function timeZone(): DateTimeZone
    {
        return new DateTimeZone('Europe/Istanbul');
    }

    public static function toFloat(string $text): float
    {
        return (float)str_replace(['.', ','], ['', '.'], $text);
    }

    public static function replace(array $replaces, $symbol): string
    {
        return str_replace(array_keys($replaces), array_values($replaces), $symbol);
    }

    public function get(): array
    {
        try {
            $this->merge((new BtcTurk)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new Garanti)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new YapiKredi)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new HalkBank)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new EnPara)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new AkBank)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new IsBankasi)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new KuveytTurk)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new Ziraat)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new CepteTeb)->get());
        } catch (\Exception $e) {
            
        }

        try {
            $this->merge((new Kocak)->get());
        } catch (\Exception $e) {
            
        }

        return $this->rates;
    }
}
