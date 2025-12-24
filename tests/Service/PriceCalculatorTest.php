<?php

namespace App\Tests\Service;

use App\Service\PriceCalculator;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    public function testCalculateWithTaxAndCoupon()
    {
        $calc = new PriceCalculator();
        $price = $calc->calculate(100, 'GR123456789', ['type' => 'percent', 'value' => 6]);
        $this->assertEquals(116.56, $price);
    }
    // Дайте больше тестовых случаев...
}