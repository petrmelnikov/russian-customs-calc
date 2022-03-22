<?php

use App\Calc;
use App\ExchangeApi;
use App\PolexpCalcShipping;
use App\Utils;

require_once __DIR__ . '/vendor/autoload.php';

$now = new \DateTime();

$priceRaw = $_POST['price'] ?? 0;
$currency = $_POST['currency'] ?? ExchangeApi::USD;
$calculateShipping = isset($_POST['calculate_shipping']) ? true : false;
$currentYear = (int) ($_POST['current_year'] ?? $now->format('Y'));
$weight = (float) ($_POST['weight'] ?? 1.0);
$shippingType = ($_POST['shipping_type'] ?? PolexpCalcShipping::PE_STANDART);

$currencyAutodetected = Calc::autodetectCurrency($priceRaw);
$price = Utils::filterMoneyValue($priceRaw);
$currency = $currencyAutodetected ?? $currency;

$calc = new Calc($currentYear);
$calculationResults = $calc->calculateTax($price, $currency);

$currencies = ExchangeApi::getCurrencies();
$shippingTypes = PolexpCalcShipping::getShippingTypes();

if ($calculateShipping) {
    $calculationResults = $calc->calculateShipping($calculationResults, $weight, $shippingType);
}

$years = [
    2019,
    2020,
    2022,
];

foreach ($years as $key => $year) {
    $newKey = $year . ' ('.Calc::TAX_FREE_MAX_EUR[$year].'€ / '. (Calc::TAX[$year] * 100 - 100) .'%)';
    $years[$newKey] = $year;
    unset($years[$key]);
}

require_once 'web/main.html.php';
