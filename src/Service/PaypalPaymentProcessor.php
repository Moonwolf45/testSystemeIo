<?php

namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor as ExternalPaypal;

class PaypalPaymentProcessor implements PaymentProcessorInterface
{
    private ExternalPaypal $processor;

    public function __construct()
    {
        $this->processor = new ExternalPaypal();
    }

    public function pay(float $amount): bool
    {
        return $this->processor->pay($amount);
    }
}