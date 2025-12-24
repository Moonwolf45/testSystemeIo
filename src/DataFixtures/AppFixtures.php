<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Coupon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Продукты
        $products = [
            ['name' => 'Iphone', 'price' => 100.00],
            ['name' => 'Headphones', 'price' => 20.00],
            ['name' => 'Case', 'price' => 10.00],
        ];
        foreach ($products as $data) {
            $prod = new Product();
            $prod->setName($data['name']);
            $prod->setPrice($data['price']);
            $manager->persist($prod);
        }

        // Купоны
        $coupons = [
            ['code' => 'D15', 'type' => 'fixed', 'value' => 15.00], // Фиксированная скидка
            ['code' => 'P10', 'type' => 'percent', 'value' => 10.00], // Процентная скидка
        ];
        foreach ($coupons as $data) {
            $coup = new Coupon();
            $coup->setCode($data['code']);
            $coup->setType($data['type']);
            $coup->setValue($data['value']);
            $manager->persist($coup);
        }

        $manager->flush();
    }
}