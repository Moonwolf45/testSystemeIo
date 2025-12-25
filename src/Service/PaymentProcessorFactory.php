<?php

namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor as ExternalStripe;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor as ExternalPaypal;

class PaymentProcessorFactory
{
    public function getProcessor(string $type): PaymentProcessorInterface
    {
        if ($type === 'stripe') {
            $externalStripe = new ExternalStripe();

            return new StripePaymentProcessor($externalStripe);
        } elseif ($type === 'paypal') {
            $externalPaypal = new ExternalPaypal();

            return new PaypalPaymentProcessor($externalPaypal);
        } else {
            throw new \InvalidArgumentException("Unsupported payment type: {$type}");
        }
    }
}