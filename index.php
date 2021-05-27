<?php

use App\Calc;
use App\ExchangeApi;
use App\PolexpCalcShipping;
use App\Utils;

require_once __DIR__ . '/vendor/autoload.php';

$priceRaw = $_POST['price'] ?? 0;
$currency = $_POST['currency'] ?? ExchangeApi::USD;
$calculateShipping = isset($_POST['calculate_shipping']) ? true : false;
$currentYear = (int) ($_POST['current_year'] ?? 2020);
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

$years = [2019, 2020];

require_once 'web/main.html.php';
