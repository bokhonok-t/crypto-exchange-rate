<?php

namespace App\Service;

use App\DTO\DateFilterDto;
use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeService
{
    public function __construct(
        protected HttpClientInterface $client,
        protected CacheInterface $cache,
        protected EntityManagerInterface $em
    ) {}

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function getRates(?DateFilterDto $filterDto = null): array
    {
        $current = clone $filterDto?->from;
        $to = $filterDto?->to;
        $allRates = [];

        while ($current <= $to) {
            $rates = $this->getRatesByDate($current);
            $allRates = array_merge($allRates, $rates);
            $current = $current->modify('+1 day');
        }

        return $allRates;
    }

    public function getRatesByDate(DateTimeImmutable $date): array
    {
        $key = $this->buildCacheKey($date);

        return $this->cache->get($key, function (ItemInterface $item) use ($date) {
            $item->expiresAfter(3600); // Cache for 1 hour

            /** @var ExchangeRateRepository $ratesRepository */
            $ratesRepository = $this->em->getRepository(ExchangeRate::class);
            $rates = $ratesRepository->getFiltered($date, $date);

            $response = [];

            foreach ($rates as $rate) {
                $response[$rate->getDate()->format('Y-m-d')] = $this->formatResponse($rate);
            }

            return $response;
        });
    }

    public function buildCacheKey(DateTimeInterface $dateTime): string
    {
        return 'rates_' . $dateTime->format('Y-m-d');
    }

    public function formatResponse(ExchangeRate $rate): array
    {
        return [
            'currencyFrom' => $rate->getCurrencyFrom(),
            'currencyTo' => $rate->getCurrencyTo(),
            'rate' => $rate->getRate(),
            'date' => $rate->getDate()->format('Y-m-d H:i'),
        ];
    }
}