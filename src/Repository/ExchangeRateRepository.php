<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ExchangeRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeRate[]    findAll()
 * @method ExchangeRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function getRatesForDatesAndCurrency($startDate, $endDate, $currency = null)
    {
        $query = $this->createQueryBuilder('exchangeRate')
            ->where('exchangeRate.date >= :startDate')
            ->andWhere('exchangeRate.date <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);
        if ($currency) {
            $query->andWhere('exchangeRate.currency = :currency')
                ->setParameter('currency', $currency);
        }
        return $query->orderBy('exchangeRate.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
