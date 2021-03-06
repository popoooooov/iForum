<?php

/*
 * This file is part of the "php-paradise/array-keys-converter" package.
 * (c) Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Topics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method null|Topics find($id, $lockMode = null, $lockVersion = null)
 * @method null|Topics findOneBy(array $criteria, array $orderBy = null)
 * @method Topics[]    findAll()
 * @method Topics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Topics::class);
    }

    public function getQuery($object)
    {
        $qb = $this->createQueryBuilder('q')
            ->select('q.id', 'q.name')
            ->where('q.name LIKE :object')
            ->setParameter('object', $object['question'])
            ->getQuery();

        return $qb->execute();
    }
}
