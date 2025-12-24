<?php

namespace App\Dto;

use App\Validator\TaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $product;

    #[Assert\NotBlank]
    #[TaxNumber]
    public string $taxNumber;

    #[Assert\Optional]
    public ?string $couponCode = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['paypal', 'stripe'])]
    public string $paymentProcessor;
}