<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Messages::class);
    }

    public function getQuery($object)
    {
                $qb = $this->createQueryBuilder('q')
                    ->select('IDENTITY(q.topics)','q.text')
                    ->andWhere("q.text LIKE :object")
                    ->setParameter("object",'%'.$object['question'].'%')
                    ->getQuery();
            return $qb->execute();
    }

    public function lastMessage($topic)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->setMaxResults(1);
        $qb->orderBy('t.id','DESC');

        return $qb->getQuery()->getSingleResult();
    }


//    /**
//     * @return Messages[] Returns an array of Messages objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Messages
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
