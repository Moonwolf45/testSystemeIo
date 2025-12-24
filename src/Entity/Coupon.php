<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\CouponType;

/**
 * @ORM\Entity
 */
class Coupon
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private string $code;

    /**
     * @ORM\Column(type="string", length=10, enumType: CouponType::class) // 'fixed' или 'percent'
     */
    private string $type = 'percent';

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private float $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): ?CouponType
    {
        return $this->type;
    }

    public function setType(CouponType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }
}