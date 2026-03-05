<?php

// src/Repository/AllPasswordRepository.php
namespace App\Repository;

use App\Entity\User;
use App\Entity\AllPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AllPassword>
 *
 * @method AllPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method AllPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method AllPassword[]    findAll()
 * @method AllPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllPassword::class);
    }

    public function searchByQuery(?string $query, User $user): array
    {
        if (!$query) {
            return [];
        }
    
        $qb = $this->createQueryBuilder('e')
            ->where('e.user = :user')
            ->andWhere('e.site LIKE :query')
            ->setParameter('user', $user)
            ->setParameter('query', '%' . $query . '%');
    
        return $qb->getQuery()->getResult();
    }

   
}