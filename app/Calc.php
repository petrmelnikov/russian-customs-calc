<?php

namespace App;

use App\ExchangeApi;
use App\PolexpCalcShipping;
use App\DataTransferObjects\CalculationResult;

class Calc
{
    public const CURRENCY_SIGN_MAP = [
        'CDN$' => ExchangeApi::CAD,
        '$' => ExchangeApi::USD,
        '€' => ExchangeApi::EUR,
        '₽' => ExchangeApi::RUB,
        '£' => ExchangeApi::GBP,
    ];

    public const TAX_FREE_MAX_EUR = [
        2019 => 500,
        2020 => 200,
        2022 => 1000,
    ];

    public const TAX = [
        2019 => 1.3, // 30%
        2020 => 1.15, // 15%
        2022 => 1.15, // 15%
    ];

    private $exchangeApi = null;
    private $polexpCalcShipping = null;
    private $currnetYear = null;

    public function __construct(int $currentYear = 2019)
    {
        $this->exchangeApi = new ExchangeApi();
        $this->polexpCalcShipping = new PolexpCalcShipping();
        $this->currnetYear = $currentYear;
    }

    public function convert($value, $fromCurrency, $toCurrency)
    {
        $rates = $this->exchangeApi->getRates($fromCurrency);
        return $rates->rates->$toCurrency * $value;
    }

    public function calculateShipping(array $calculationResults, float $weight, string $shippingType)
    {
        $polexpCalcShipping = new PolexpCalcShipping();

        /** @var CalculationResult $calculationResultUsd */
        $calculationResultUsd = $calculationResults[ExchangeApi::USD];
        $taxUsd = $calculationResultUsd->getTax();
        $priceUsd = $calculationResultUsd->getPrice();

        $shipping = $polexpCalcShipping->calculate($weight, $priceUsd, $taxUsd, $shippingType);
        foreach ($calculationResults as &$calculationResult) {
            $calculationResult = $this->calculateShippingPriceForCurrency($calculationResult, $shipping);
        }

        return $calculationResults;
    }

    public function calculateTax(float $price, string $baseCurrency): array
    {
        $rates = $this->exchangeApi->getRates($baseCurrency);

        $taxFreeMax = $this->getTaxFreeMaxEur() / $rates->rates->EUR;

        $valueAboveTaxFree = $price - $taxFreeMax;

        $tax = 0;
        if ($valueAboveTaxFree > 0) {
            $valueAboveTaxFreeWithTax = $valueAboveTaxFree * $this->getTax();
            $tax = $valueAboveTaxFreeWithTax - $valueAboveTaxFree;
        }

        $calculationResults = [];
        foreach ($rates->rates as $currency => $rate) {
            $calculationResults[$currency] = (new CalculationResult($currency))
                ->setPrice(round($price * $rate, 2))
                ->setPriceAboveTaxFree(round(($valueAboveTaxFree > 0 ? $valueAboveTaxFree : 0) * $rate, 2))
                ->setTax(round($tax * $rate));
        }
        return $calculationResults;
    }

    public static function autodetectCurrency(string $rawValue): ?string
    {
        $result = null;
        foreach (self::CURRENCY_SIGN_MAP as $sign => $code) {
            if (false !== stristr($rawValue, $sign)) {
                $result = $code;
                break;
            }
        }
        return $result;
    }

    private function calculateShippingPriceForCurrency(CalculationResult $calculationResult, float $shippingPriceUsd): CalculationResult
    {
        $shippingPrice = round(
            $this->convert($shippingPriceUsd, ExchangeApi::USD, $calculationResult->getCurrency()),
            2
        );
        $calculationResult->setShipping($shippingPrice);

        return $calculationResult;
    }

    private function getTaxFreeMaxEur(): float
    {
        return self::TAX_FREE_MAX_EUR[$this->currnetYear];
    }

    private function getTax(): float
    {
        return self::TAX[$this->currnetYear];
    }
}
