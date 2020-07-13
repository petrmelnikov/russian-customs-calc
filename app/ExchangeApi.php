<?php

namespace App;

use \Exception;

class ExchangeApi {

    const API_RATES_URL = 'https://api.ratesapi.io/api/latest';

    public static function getCurrencies(): array {
        return [
            'EUR',
            'RUB',
            'USD',
            'CAD',
        ];
    }

    public function getRates(string $baseCurrency = 'EUR'): \StdClass {
        $currencies = self::getCurrencies();

        if (!in_array($baseCurrency, $currencies)) {
            throw new Exception('Wrong currency!');
        }

        $params = [
            'symbols' => implode(',', array_diff($currencies, [$baseCurrency])),
            'base' => $baseCurrency,
        ];

        $url = self::API_RATES_URL . '?' . http_build_query($params);

        $rates = $this->sendRequest($url);

        if (isset($rates->rates)) {
            $rates->rates->$baseCurrency = 1;
        }

        return $rates;
    }

    private function sendRequest(string $url): \StdClass {
        $result = new \stdClass();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
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
