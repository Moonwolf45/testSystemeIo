<?php

namespace App\Service;

interface PaymentProcessorInterface
{
    public function pay(float $amount): bool;
}