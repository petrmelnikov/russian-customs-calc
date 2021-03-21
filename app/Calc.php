<?php

namespace App;

use App\ExchangeApi;

class Calc {
    public const CURRENCY_SIGN_MAP = [
        'CDN$' => 'CAD',
        '$' => 'USD',
        '€' => 'EUR',
        '₽' => 'RUB',
    ];

    public const TAX_FREE_MAX_EUR = [
        2019 => 500,
        2020 => 200,
    ];

    public const TAX = [
        2019 => 1.3, // 30%
        2020 => 1.15, // 15%
    ];

    private $exchangeApi = null;
    private $currnetYear = null;

    public function __construct(int $currentYear = 2019)
    {
        $this->exchangeApi = new ExchangeApi();
        $this->currnetYear = $currentYear;
    }

    public function calculateTax(float $price, string $currency): array
    {
        $rates = $this->exchangeApi->getRates($currency);
        $taxFreeMax = $this->getTaxFreeMaxEur() / $rates->rates->EUR;

        $valueAboveTaxFree = $price - $taxFreeMax;

        $tax = 0;
        if ($valueAboveTaxFree > 0) {
            $valueAboveTaxFreeWithTax = $valueAboveTaxFree * $this->getTax();
            $tax = $valueAboveTaxFreeWithTax - $valueAboveTaxFree;
            $priceWithTaxUsd = $tax + $price;
        } else {
            $priceWithTaxUsd = $price;
        }

        $result = [];
        foreach ($rates->rates as $currency => $rate) {
            $result[$currency] = [
                'price' => round($price * $rate, 2),
                'price_above_tax_free_value' => round(($valueAboveTaxFree > 0 ? $valueAboveTaxFree : 0) * $rate, 2),
                'tax' => round($tax * $rate),
                'price_with_tax' => round($priceWithTaxUsd * $rate),
            ];
        }
        return $result;
    }

    public static function autodetectCurrency(string $rawValue): ?string {
        $result = null;
        foreach (self::CURRENCY_SIGN_MAP as $sign => $code) {
            if (false !== stristr($rawValue, $sign)) {
                $result = $code;
                break;
            }
        }
        return $result;
    }

    private function getTaxFreeMaxEur(): float {
        return self::TAX_FREE_MAX_EUR[$this->currnetYear];
    }

    private function getTax(): float {
        return self::TAX[$this->currnetYear];
    }
}
