<?php

namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PaymentProcessorFactory
{
    private PaypalPaymentProcessor $paypal;
    private StripePaymentProcessor $stripe;

    public function __construct(PaypalPaymentProcessor $paypal, StripePaymentProcessor $stripe)
    {
        $this->paypal = $paypal;
        $this->stripe = $stripe;
    }

    public function getProcessor(string $type): PaymentProcessorInterface
    {
        return match ($type) {
            'paypal' => $this->paypal,
            'stripe' => $this->stripe,
        };
    }
}