<?php

namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor as ExternalPaypal;

class PaypalPaymentProcessor implements PaymentProcessorInterface
{
    private ExternalPaypal $processor;

    public function __construct(ExternalPaypal $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @throws \Exception
     */
    public function pay(float $amount): bool
    {
        $this->processor->pay($amount);

        return true;
    }
}