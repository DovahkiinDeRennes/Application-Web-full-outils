<?php

// src/Repository/AllPasswordRepository.php
namespace App\Repository;

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

    // public function add(AllPassword $entity, bool $flush = false): void
    // {
    //     $this->_em->persist($entity);
    //     if ($flush) {
    //         $this->_em->flush();
    //     }
    // }

    // public function remove(AllPassword $entity, bool $flush = false): void
    // {
    //     $this->_em->remove($entity);
    //     if ($flush) {
    //         $this->_em->flush();
    //     }
    // }
}