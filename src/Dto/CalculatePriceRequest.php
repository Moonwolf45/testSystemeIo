<?php

namespace App\Dto;

use App\Validator\TaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

class CalculatePriceRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $product;

    #[Assert\NotBlank]
    #[TaxNumber]
    public string $taxNumber;

    #[Assert\Optional]
    public ?string $couponCode = null;
}