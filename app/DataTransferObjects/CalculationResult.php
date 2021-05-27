<?php

namespace App\DataTransferObjects;

use App\ExchangeApi;

class CalculationResult
{
    private $price = 0.0;
    private $priceAboveTaxFree = 0.0;
    private $tax = 0.0;
    private $shipping = 0.0;
    private $currency = null;

    public function __construct(string $currency)
    {
        $this->currency = $currency;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getPriceAboveTaxFree(): float
    {
        return $this->priceAboveTaxFree;
    }

    public function setPriceAboveTaxFree(float $priceAboveTaxFree)
    {
        $this->priceAboveTaxFree = $priceAboveTaxFree;
        return $this;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax)
    {
        $this->tax = $tax;
        return $this;
    }

    public function getShipping(): float
    {
        return $this->shipping;
    }

    public function setShipping(float $shipping)
    {
        $this->shipping = $shipping;
        return $this;
    }

    public function getPriceWithTax(): float
    {
        return $this->getPrice() + $this->getTax();
    }

    public function getTotal(): float
    {
        return $this->getPriceWithTax() + $this->getShipping();
    }
}
