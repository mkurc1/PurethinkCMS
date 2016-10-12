<?php

namespace Purethink\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;

class LanguageRepository extends EntityRepository
{
    public function getPublicLanguages()
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('m')
            ->leftJoin('a.media', 'm')
            ->where('a.enabled = true')
            ->orderBy('a.name');

        return $qb->getQuery()->getResult();
    }
}
