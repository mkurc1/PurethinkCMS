<?php

namespace Purethink\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ContactRepository extends EntityRepository
{
    public function countContactRequest() : int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->where('c.response = false')
            ->getQuery()
            ->getSingleScalarResult();
    }
}