<?php

namespace App\Tests\Service;

use App\Enum\CouponType;
use App\Service\PriceCalculator;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    private PriceCalculator $calc;

    protected function setUp(): void
    {
        $this->calc = new PriceCalculator();
    }

    public function testCalculateWithPercentCoupon(): void
    {
        $price = $this->calc->calculate(100, 'GR123456789', ['type' => CouponType::Percent, 'value' => 6]);
        $this->assertEquals(116.56, $price);
    }

    // Без купона, для разных стран
    public function testCalculateWithoutCouponGermany(): void
    {
        $price = $this->calc->calculate(100, 'DE123456789');
        $this->assertEquals(119.0, $price); // 100 * 1.19
    }

    public function testCalculateWithoutCouponItaly(): void
    {
        $price = $this->calc->calculate(100, 'IT123456789');
        $this->assertEquals(122.0, $price); // 100 * 1.22
    }

    public function testCalculateWithoutCouponFrance(): void
    {
        $price = $this->calc->calculate(100, 'FR123456789');
        $this->assertEquals(120.0, $price); // 100 * 1.20
    }

    public function testCalculateWithoutCouponGreece(): void
    {
        $price = $this->calc->calculate(100, 'GR123456789');
        $this->assertEquals(124.0, $price); // 100 * 1.24
    }

    // Без купона, неизвестная страна (taxRate = 0)
    public function testCalculateWithoutCouponUnknownCountry(): void
    {
        $price = $this->calc->calculate(100, 'XX123456789');
        $this->assertEquals(100.0, $price);
    }

    // Fixed купон, вычитание меньше basePrice
    public function testCalculateWithFixedCouponPartialDiscountGermany(): void
    {
        $price = $this->calc->calculate(100, 'DE123456789', ['type' => CouponType::Fixed, 'value' => 10]);
        $this->assertEquals(107.10, $price);
    }

    // Fixed купон, вычитание равно basePrice (должно быть 0 до налога)
    public function testCalculateWithFixedCouponFullDiscountItaly(): void
    {
        $price = $this->calc->calculate(100, 'IT123456789', ['type' => CouponType::Fixed, 'value' => 100]);
        $this->assertEquals(0.0, $price); // max(0, 0) * 1.22 = 0
    }

    // Fixed купон, вычитание больше basePrice (max(0))
    public function testCalculateWithFixedCouponOverDiscountFrance(): void
    {
        $price = $this->calc->calculate(50, 'FR123456789', ['type' => CouponType::Fixed, 'value' => 100]);
        $this->assertEquals(0.0, $price); // max(0, 50 - 100) * 1.20 = 0
    }

    // Percent купон, 0%
    public function testCalculateWithPercentCouponZeroGreece(): void
    {
        $price = $this->calc->calculate(100, 'GR123456789', ['type' => CouponType::Percent, 'value' => 0]);
        $this->assertEquals(124.0, $price); // Без скидки
    }

    // Percent купон, 10%
    public function testCalculateWithPercentCouponTenGermany(): void
    {
        $price = $this->calc->calculate(100, 'DE123456789', ['type' => CouponType::Percent, 'value' => 10]);
        $this->assertEquals(107.10, $price); // 100 * 0.9 * 1.19 = 90 * 1.19 = 107.1
    }

    // Percent купон, 50%
    public function testCalculateWithPercentCouponFiftyItaly(): void
    {
        $price = $this->calc->calculate(200, 'IT123456789', ['type' => CouponType::Percent, 'value' => 50]);
        $this->assertEquals(122.0, $price); // 200 * 0.5 * 1.22 = 100 * 1.22 = 122
    }

    // Percent купон, 100%
    public function testCalculateWithPercentCouponHundredFrance(): void
    {
        $price = $this->calc->calculate(100, 'FR123456789', ['type' => CouponType::Percent, 'value' => 100]);
        $this->assertEquals(0.0, $price); // 0 * 1.20 = 0
    }

    // Percent купон, неизвестная страна
    public function testCalculateWithPercentCouponUnknownCountry(): void
    {
        $price = $this->calc->calculate(100, 'XX123456789', ['type' => CouponType::Percent, 'value' => 20]);
        $this->assertEquals(80.0, $price); // 100 * 0.8 * 1.0 = 80
    }

    // Разные basePrice
    public function testCalculateWithSmallBasePrice(): void
    {
        $price = $this->calc->calculate(1, 'GR123456789', ['type' => CouponType::Percent, 'value' => 10]);
        $this->assertEquals(1.12, $price);
    }

    public function testCalculateWithLargeBasePrice(): void
    {
        $price = $this->calc->calculate(1000, 'IT123456789');
        $this->assertEquals(1220.0, $price); // 1000 * 1.22
    }

    // Проверка округления
    public function testRounding(): void
    {
        // Для точности, если результат 116.556, должен округлиться до 116.56
        $price = $this->calc->calculate(100, 'GR123456789', ['type' => CouponType::Percent, 'value' => 6]);
        $this->assertEquals(116.56, $price);
    }
}