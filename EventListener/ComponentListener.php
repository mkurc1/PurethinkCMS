<?php

namespace Purethink\CMSBundle\EventListener;

use Purethink\CMSBundle\Entity\Component;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;

class ComponentListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Component) {
            $entity->setSlug($entity->getExtension()->getName());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Component && $entity) {
            $entity->setSlug($entity->getExtension()->getName());
        }
    }
}