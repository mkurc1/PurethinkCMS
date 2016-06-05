<?php

namespace Purethink\CMSBundle\EventListener;

use Purethink\CMSBundle\Entity\Component;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class ComponentListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Component) {
            $entity->setSlug($entity->getExtension()->getName());
        }
    }
}