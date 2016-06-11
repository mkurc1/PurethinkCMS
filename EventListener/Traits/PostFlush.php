<?php

namespace Purethink\CMSBundle\EventListener\Traits;

use Doctrine\ORM\Event\PostFlushEventArgs;

trait PostFlush
{
    /** @var bool */
    protected $setForUpdate;

    public function postFlush(PostFlushEventArgs $args)
    {
        if ($this->setForUpdate) {
            $this->setForUpdate = false;
            $args->getEntityManager()->flush();
        }
    }
}