<?php

namespace App\Repository;

use App\Entity\Optione;
use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }


    /**
     * @param PropertySearch $search
     * @return Query
     */
    public function findAllVisibleQuery(PropertySearch $search) : Query {
        $query = $this->findVisibleQuery();

        if ($search->getMaxPrice()) {
            $query = $query
                    ->andwhere(('p.price <= :maxPrice'))
                    ->setParameter('maxPrice', $search->getMaxPrice());
        }
        if ($search->getMinSurface()) {
            $query = $query
                ->andwhere(('p.surface >= :minSurface'))
                ->setParameter('minSurface', $search->getMinSurface());
        }
        if ($search->getOptiones()->count() > 0) {
            $k = 0;
          foreach ($search->getOptiones() as $k => $optione){
              $k++;
              $query = $query
                  ->andWhere(":optione$k MEMBER OF p.optiones")
                  ->setParameter("optione$k" ,  $optione);
          }
        }
            return $query->getQuery();
    }

    /**
     * @return Property[]
     */
    public function findLatest() : array {
        return $this->findVisibleQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    private function findVisibleQuery() : QueryBuilder{
        return $this->createQueryBuilder('p')
//            ->innerJoin(Optione::class, 'op', Join::WITH, 'p.id = op.id')
//            ->andWhere('op.id = p.id')
            ->andwhere('p.sold = false');
    }
    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
