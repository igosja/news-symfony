<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * @extends ServiceEntityRepository<Language>
 *
 * @method Language|null find($id, $lockMode = null, $lockVersion = null)
 * @method Language|null findOneBy(array $criteria, array $orderBy = null)
 * @method Language[]    findAll()
 * @method Language[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }

    public function add(Language $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Language $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByFilters(array $criteria = [], array $orderBy = null, $limit = null, $offset = null)
    {
        $query = $this->createQueryBuilder('l');

        $parameters = [];
        foreach ($criteria as $key => $value) {
            if ('' === $value) {
                continue;
            }

            if ('id' === $key) {
                $query->andWhere('l.id = :id');
                $parameters[] = new Parameter('id', $value, Types::INTEGER);
            } elseif ('name' === $key) {
                $query->andWhere('l.name LIKE :name');
                $parameters[] = new Parameter('name', '%' . $value . '%', Types::STRING);
            } elseif ('code' === $key) {
                $query->andWhere('l.code LIKE :code');
                $parameters[] = new Parameter('code', '%' . $value . '%', Types::STRING);
            }
        }

        if ($parameters) {
            $query->setParameters(new ArrayCollection($parameters));
        }

        if ($orderBy) {
            $query->addOrderBy('l.'.$orderBy['sort'], $orderBy['order']);
        }

        return $query
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Language[] Returns an array of Language objects
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

//    public function findOneBySomeField($value): ?Language
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
