<?php

namespace App;

use Exception;
use Shieldon\SimpleCache\Cache;

class ExchangeApi
{
    private const API_RATES_URL = 'http://api.exchangeratesapi.io/v1/latest';
    private const ACCESS_KEY = '16035d27fc6db791eb5fef930ea82184';

    private const RATES_CACHE_KEY = 'rates';
    private const RATES_CACHE_TTL = 60*60;

    public const EUR = 'EUR';
    public const RUB = 'RUB';
    public const USD = 'USD';
    public const CAD = 'CAD';
    public const GBP = 'GBP';

    private $cache = null;

    public function __construct()
    {
        $this->cache = new Cache('file', [
            'storage' => __DIR__ . '/../cache'
        ]);
    }

    public static function getCurrencies(): array
    {
        return [
            self::EUR,
            self::RUB,
            self::USD,
            self::CAD,
            self::GBP,
        ];
    }

    public function getRates(string $baseCurrency = self::EUR): \StdClass
    {
        $currencies = self::getCurrencies();

        if (!in_array($baseCurrency, $currencies)) {
            throw new Exception('Wrong currency!');
        }

        $params = [
            'symbols' => implode(',', $currencies),
            'access_key' => self::ACCESS_KEY,
        ];

        $url = self::API_RATES_URL . '?' . http_build_query($params);

        $rates = $this->getRatesCached($url);

        if ($rates->base !== $baseCurrency) {
            $rates->rates = $this->recalculateBase(
                $rates->rates,
                $baseCurrency,
                $rates->base
            );
        }

        return $rates;
    }

    private function getRatesCached(string $url): \StdClass
    {
        if (empty($this->cache->get(self::RATES_CACHE_KEY))) {
            $rates = $this->sendRequest($url);
            $this->cache->set(self::RATES_CACHE_KEY, $rates, self::RATES_CACHE_TTL);
        } else {
            $rates = $this->cache->get(self::RATES_CACHE_KEY);
        }
        return $rates;
    }

    private function recalculateBase(\StdClass $rates, string $newBaseCurrency, string $oldBaseCurrncy): \StdClass
    {
        $oldToNew = $rates->$oldBaseCurrncy / $rates->$newBaseCurrency;
        foreach ($rates as $currency => &$rate) {
            if ($newBaseCurrency === $currency) {
                $rate = 1;
            } else {
                $rate = $oldToNew * $rate;
            }
        }
        return $rates;
    }

    private function sendRequest(string $url): \StdClass
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode === 200) {
            $result = json_decode($response);
        } else {
            throw new \Exception($response);
        }

        return $result;
    }
}
