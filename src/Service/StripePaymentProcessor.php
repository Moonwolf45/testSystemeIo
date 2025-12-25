<?php

namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor as ExternalStripe;

class StripePaymentProcessor implements PaymentProcessorInterface
{
    private ExternalStripe $processor;

    public function __construct(ExternalStripe $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @throws \Exception
     */
    public function pay(float $amount): bool
    {
        return $this->processor->processPayment($amount);
    }
}