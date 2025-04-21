<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeRate>
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    /**
     * @return ExchangeRate[]
     */
    public function getFiltered(?DateTimeImmutable $dateFrom = null, ?DateTimeImmutable $dateTo = null): array
    {
        $qb = $this->createQueryBuilder('er');

        if ($dateFrom !== null) {
            $qb
                ->where('er.date >= :dateFrom')
                ->setParameter('dateFrom', $dateFrom->setTime(0, 0));
        }

        if ($dateTo !== null) {
            $qb
                ->andWhere('er.date <= :dateTo')
                ->setParameter('dateTo', $dateTo->setTime(23, 59));
        }

        return $qb->getQuery()->getResult();
    }
}
