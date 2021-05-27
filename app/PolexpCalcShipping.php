<?php

namespace App;

class PolexpCalcShipping
{
    public const PE_STANDART = 'PE_STANDART';
    public const PE_PICKPOINT = 'PE_PICKPOINT';

    private const SHIPPING_VALUES = [
        self::PE_STANDART => [
                'first' => 24.75,
                'next' => 12.85,
                'step' => 1, //kg
                'max_weight' => 30, //kg
        ],

        self::PE_PICKPOINT => [
            'first' => 12.95,
            'next' => 6.3,
            'step' => 0.5, //kg
            'max_weight' => 15, //kg
        ]
    ];

    private const INSURANCE_PRICE = [
        101 => 0, //0-99
        601 => 5, //100-600
        1001 => 7.50, //600-1000
    ];
    private const INSURANCE_PRICE_MAX = 15;

    private const CUSTOMS_FEE_FIXED = 7.5;

    private const BANK_COMISSION = 0.0375;

    private const BANK_CONVERSION = 0.04;

    public static function getShippingTypes(): array
    {
        return array_keys(self::SHIPPING_VALUES);
    }

    public function calculate(float $weight, float $price, float $tax, string $shippingType = self::PE_STANDART)
    {
        $shippingValues = self::SHIPPING_VALUES[$shippingType];

        $weightSteps = ceil($weight / $shippingValues['step']);
        $shippingPrice = $shippingValues['first'] + (($weightSteps > 1) ? ($weightSteps - 1) * $shippingValues['next'] : 0);

        $bankConverisionPrice = $tax * self::BANK_CONVERSION; //conversion_amount

        $insurancePrice = $this->calculateInsurance($price);

        $total = $shippingPrice + $tax + self::CUSTOMS_FEE_FIXED + $insurancePrice + $bankConverisionPrice;

        $bankComission = round($total * self::BANK_COMISSION, 2);

        return $total + $bankComission - $tax;
    }

    private function calculateInsurance(float $price)
    {
        foreach (self::INSURANCE_PRICE as $thresold => $fee) {
            if ($price < $thresold) {
                return $fee;
            }
        }
        return self::INSURANCE_PRICE_MAX;
    }
}
