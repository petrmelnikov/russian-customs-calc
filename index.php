<?php

use App\Calc;
use App\ExchangeApi;
use App\Utils;

require_once __DIR__ . '/vendor/autoload.php';

$priceRaw = $_POST['price'] ?? 0;
$currency = $_POST['currency'] ?? 'USD';

$currencyAutodetected = Calc::autodetectCurrency($priceRaw);
$price = Utils::filterMoneyValue($priceRaw);
$currency = $currencyAutodetected ?? $currency;
$currentYear = (int) ($_POST['current_year'] ?? 2020);

$calc = new Calc($currentYear);
$result = $calc->calculateTax($price, $currency);
$currencies = ExchangeApi::getCurrencies();

$years = [2019, 2020];

require_once 'web/main.html.php';
