<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Header;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Header|null find($id, $lockMode = null, $lockVersion = null)
 * @method Header|null findOneBy(array $criteria, array $orderBy = null)
 * @method Header[]    findAll()
 * @method Header[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Header::class);
    }
}
