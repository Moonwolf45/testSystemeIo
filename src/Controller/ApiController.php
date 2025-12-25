<?php
namespace App\Controller;

use App\Dto\CalculatePriceRequest;
use App\Dto\PurchaseRequest;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\PriceCalculator;
use App\Service\PaymentProcessorFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController
{

    private PriceCalculator $priceCalculator;
    private SerializerInterface $serializer;

    public function __construct(PriceCalculator $priceCalculator, SerializerInterface $serializer)
    {
        $this->priceCalculator = $priceCalculator;
        $this->serializer = $serializer;
    }

    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(
        Request $req,
        ValidatorInterface $validator,
        ProductRepository $productRepository,
        CouponRepository $couponRepository
    ): JsonResponse {
        $data = $req->toArray();
        $dto = $this->serializer->denormalize($data, CalculatePriceRequest::class);
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string)$errors], 400);
        }

        $product = $productRepository->findOneById($dto->product);
        if (!$product) {
            return new JsonResponse(['error' => 'Продукт не найден'], 400);
        }

        $coupon = null;
        if ($dto->couponCode) {
            $coupon = $couponRepository->findOneByCode(['code' => $dto->couponCode]);
            if (!$coupon) {
                return new JsonResponse(['error' => 'Неверный купон'], 400);
            }
            $coupon = ['type' => $coupon->getType(), 'value' => $coupon->getValue()];
        }

        $price = $this->priceCalculator->calculate($product->getPrice(), $dto->taxNumber, $coupon);

        return new JsonResponse(['price' => $price], 200);
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(
        Request $req,
        ValidatorInterface $validator,
        PaymentProcessorFactory $ppFactory,
        ProductRepository $productRepository,
        CouponRepository $couponRepository
    ): JsonResponse {
        $data = $req->toArray();
        $dto = $this->serializer->denormalize($data, PurchaseRequest::class);
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string)$errors], 400);
        }

        $product = $productRepository->findOneById($dto->product);
        if (!$product) {
            return new JsonResponse(['error' => 'Продукт не найден'], 422);
        }

        $coupon = null;
        if ($dto->couponCode) {
            $coupon = $couponRepository->findOneByCode(['code' => $dto->couponCode]);
            if (!$coupon) {
                return new JsonResponse(['error' => 'Неверный купон'], 422);
            }
            $coupon = ['type' => $coupon->getType(), 'value' => $coupon->getValue()];
        }

        $price = $this->priceCalculator->calculate($product->getPrice(), $dto->taxNumber, $coupon);
        $processor = $ppFactory->getProcessor($dto->paymentProcessor);
        if (!$processor->pay($price)) {
            return new JsonResponse(['error' => 'Платеж не прошел'], 422);
        }

        return new JsonResponse(['message' => 'Покупка успешна'], 200);
    }
}