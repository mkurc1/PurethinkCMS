<?php

namespace Purethink\CMSBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Purethink\CMSBundle\Entity\Component;
use Purethink\CMSBundle\Entity\ComponentHasElement;
use Purethink\CMSBundle\Entity\ComponentHasValue;
use Purethink\CMSBundle\Entity\ExtensionHasField;
use Purethink\CMSBundle\EventListener\Traits\PostFlush;

class ExtensionHasFieldListener
{
    use PostFlush;

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof ExtensionHasField) {
            $components = $entity->getExtension()->getComponents();
            /** @var Component $component */
            foreach ($components as $component) {
                /** @var ComponentHasElement $element */
                foreach ($component->getElements() as $element) {
                    $componentHasValue = ComponentHasValue::getComponentHasValueType($element, $entity);
                    $element->addComponentHasValue($componentHasValue);
                    $this->setForUpdate = true;
                }
            }
        }
    }
}