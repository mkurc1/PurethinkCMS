<?php

namespace Purethink\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ExtensionRepository extends EntityRepository
{
    public function getExtensionsQb()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.name');
    }
}