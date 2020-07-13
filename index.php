<?php

use App\Calc;
use App\ExchangeApi;
use App\Utils;

require_once __DIR__ . '/vendor/autoload.php';

$price = Utils::filterMoneyValue($_POST['price'] ?? 0);
$currency = $_POST['currency'] ?? 'USD';
$currentYear = (int) ($_POST['current_year'] ?? 2020);

$calc = new Calc($currentYear);
$result = $calc->calculateTax($price, $currency);
$currencies = ExchangeApi::getCurrencies();

$years = [2019, 2020];

require_once 'web/main.html.php';
