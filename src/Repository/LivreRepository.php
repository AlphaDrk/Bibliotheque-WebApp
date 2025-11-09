<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    /**
     * QueryBuilder methods (Exercise 2)
     */
    public function findByPrixSup($x): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.prix > :x')
            ->setParameter('x', $x)
            ->getQuery()
            ->getResult();
    }

    public function findByPrixPages($x, $y): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.prix > :x')
            ->andWhere('l.nbPages < :y')
            ->setParameters(['x' => $x, 'y' => $y])
            ->getQuery()
            ->getResult();
    }

    public function findByPrixPages10($x, $y): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.prix > :x')
            ->andWhere('l.nbPages < :y')
            ->setParameters(['x' => $x, 'y' => $y])
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findByPrixPagesTrie($x, $y): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.prix > :x')
            ->andWhere('l.nbPages < :y')
            ->setParameters(['x' => $x, 'y' => $y])
            ->orderBy('l.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByPrixPages10Trie($x, $y): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.prix > :x')
            ->andWhere('l.nbPages < :y')
            ->setParameters(['x' => $x, 'y' => $y])
            ->orderBy('l.prix', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findByPrixPagesAuteurTrie($x, $y): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.prix > :x')
            ->andWhere('l.nbPages < :y')
            ->andWhere('l.auteur = :auteur')
            ->setParameters(['x' => $x, 'y' => $y, 'auteur' => 'Fabien Potencier'])
            ->orderBy('l.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * DQL versions (Exercise 3)
     */
    public function findByPrixSupDQL($x): array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT l FROM App\\Entity\\Livre l WHERE l.prix > :x')
            ->setParameter('x', $x)
            ->getResult();
    }

    public function findByPrixPagesDQL($x, $y): array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT l FROM App\\Entity\\Livre l WHERE l.prix > :x AND l.nbPages < :y')
            ->setParameters(['x' => $x, 'y' => $y])
            ->getResult();
    }

    public function findByPrixPages10DQL($x, $y): array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT l FROM App\\Entity\\Livre l WHERE l.prix > :x AND l.nbPages < :y')
            ->setParameters(['x' => $x, 'y' => $y])
            ->setMaxResults(10)
            ->getResult();
    }

    public function findByPrixPagesTrieDQL($x, $y): array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT l FROM App\\Entity\\Livre l WHERE l.prix > :x AND l.nbPages < :y ORDER BY l.prix DESC')
            ->setParameters(['x' => $x, 'y' => $y])
            ->getResult();
    }

    public function findByPrixPages10TrieDQL($x, $y): array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT l FROM App\\Entity\\Livre l WHERE l.prix > :x AND l.nbPages < :y ORDER BY l.prix DESC')
            ->setParameters(['x' => $x, 'y' => $y])
            ->setMaxResults(10)
            ->getResult();
    }

    public function findByPrixPagesAuteurTrieDQL($x, $y): array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT l FROM App\\Entity\\Livre l WHERE l.prix > :x AND l.nbPages < :y AND l.auteur = :auteur ORDER BY l.prix DESC')
            ->setParameters(['x' => $x, 'y' => $y, 'auteur' => 'Fabien Potencier'])
            ->getResult();
    }

//    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
