<?php

namespace App\Command;

use App\Entity\ExchangeRate;
use App\Service\ExchangeService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'run:sync-rates',
)]
class SyncRatesCommand extends Command
{
    public function __construct(
        protected HttpClientInterface $client,
        protected CacheInterface $cache,
        protected ExchangeService $exchangeService,
        protected EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     * @throws DecodingExceptionInterface
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->request('GET', 'https://api.coinlore.net/api/ticker/?id=90,80,257');

        if ($response->getStatusCode() !== 200) {
            throw new Exception('API error: ' . $response->getStatusCode());
        }
        $data = $response->toArray();
        $date = new DateTime();
        $this->storeToCache($data, $date);
        $this->storeToDb($data, $date);
        $output->writeln('Rates data fetched successfully.');

        return Command::SUCCESS;
    }

    private function storeToCache(array $data, DateTime $date): void
    {
        $cacheKey = $this->exchangeService->buildCacheKey($date);

        $item = $this->cache->getItem($cacheKey);
        $item->expiresAfter(3600);

        $response = [];
        if ($item->get() !== null) {
            $response = $item->get();
        }

        foreach ($data as $currencyData) {
            $response[$date->format('Y-m-d')][] = [
                'currencyFrom' => $currencyData['symbol'],
                'currencyTo' => 'USD',
                'rate' => $currencyData['price_usd'],
                'date' => $date->format('Y-m-d H:i')
            ];
        }

        $item->set($response);

        $this->cache->save($item);
    }

    private function storeToDb(array $data, DateTime $date): void
    {
        foreach ($data as $item) {
            $rate = new ExchangeRate();
            $rate->setCurrencyFrom($item['symbol']);
            $rate->setCurrencyTo('USD');
            $rate->setRate($item['price_usd']);
            $rate->setDate($date);

            $this->em->persist($rate);
        }
        $this->em->flush();
    }
}