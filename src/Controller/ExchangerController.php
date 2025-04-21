<?php

namespace App\Controller;

use App\DTO\DateFilterDto;
use App\Service\ExchangeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class ExchangerController extends AbstractController
{
    public function __construct(
        protected ExchangeService $exchangeService,
        protected EntityManagerInterface $em
    ) {
    }

    #[Route('/rates', name: 'rates_by_date', methods: ['GET'])]
    public function getRatesByDate(#[MapQueryString] DateFilterDto $filterDto): JsonResponse
    {
        $rates = $this->exchangeService->getRates($filterDto);

        return new JsonResponse($rates);
    }
}