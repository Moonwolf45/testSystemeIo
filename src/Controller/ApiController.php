<?php
namespace App\Controller;

use App\Dto\CalculatePriceRequest;
use App\Dto\PurchaseRequest;
use App\Service\PriceCalculator;
use App\Service\PaymentProcessorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController
{
    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(Request $req, SerializerInterface $serializer, ValidatorInterface $validator,
        PriceCalculator $calc, EntityManagerInterface $em
    ): JsonResponse {
        $data = $req->toArray();
        $dto = $serializer->denormalize($data, CalculatePriceRequest::class);
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $product = $em->find('App\Entity\Product', $dto->product);
        if (!$product) {
            return new JsonResponse(['error' => 'Продукт не найден'], 400);
        }

        $coupon = null;
        if ($dto->couponCode) {
            $coupon = $em->getRepository('App\Entity\Coupon')->findOneBy(['code' => $dto->couponCode]);
            if (!$coupon) {
                return new JsonResponse(['error' => 'Неверный купон'], 400);
            }
            $coupon = ['type' => $coupon->getType(), 'value' => $coupon->getValue()];
        }

        $price = $calc->calculate($product->getPrice(), $dto->taxNumber, $coupon);
        return new JsonResponse(['price' => $price], 200);
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(Request $req, SerializerInterface $serializer, ValidatorInterface $validator, PriceCalculator $calc,
        PaymentProcessorFactory $ppFactory, EntityManagerInterface $em
    ): JsonResponse {
        $data = $req->toArray();
        $dto = $serializer->denormalize($data, PurchaseRequest::class);
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $product = $em->find('App\Entity\Product', $dto->product);
        if (!$product) {
            return new JsonResponse(['error' => 'Продукт не найден'], 422);
        }

        $coupon = null;
        if ($dto->couponCode) {
            $coupon = $em->getRepository('App\Entity\Coupon')->findOneBy(['code' => $dto->couponCode]);
            if (!$coupon) {
                return new JsonResponse(['error' => 'Неверный купон'], 422);
            }
            $coupon = ['type' => $coupon->getType(), 'value' => $coupon->getValue()];
        }

        $price = $calc->calculate($product->getPrice(), $dto->taxNumber, $coupon);
        $processor = $ppFactory->getProcessor($dto->paymentProcessor);
        if (!$processor->pay($price)) {
            return new JsonResponse(['error' => 'Платеж не прошел'], 422);
        }

        return new JsonResponse(['message' => 'Покупка успешна'], 200);
    }
}