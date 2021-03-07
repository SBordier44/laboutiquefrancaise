<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, protected Security $security)
    {
        parent::__construct($registry, Order::class);
    }

    public function findSuccessOrders()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status IN (:status)')
            ->setParameter(
                'status',
                [Order::STATUS_PAID, Order::STATUS_PREPARE, Order::STATUS_SENDED, Order::STATUS_DELIVERED]
            )
            ->andWhere('o.user = :user')
            ->setParameter('user', $this->security->getUser())
            ->orderBy('o.id', 'desc')
            ->getQuery()
            ->getResult();
    }
}
