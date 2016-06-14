<?php

namespace Purethink\CMSBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Purethink\CMSBundle\Entity\Contact;
use Purethink\CMSBundle\Entity\Site;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContactListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Contact) {
            $om = $args->getObjectManager();
            /** @var Site $site */
            $site = $this->getSite($om);
            if ($site->isSendContactRequestOnEmail() && false === empty($site->getContactEmail())) {
                $this->sendContactEmailRequest($entity);
            }
        }
    }

    private function sendContactEmailRequest(Contact $contact)
    {
        $this->container->get('app.contact_email')
            ->sendContactEmailRequest($contact);
    }

    private function getSite(ObjectManager $om)
    {
        return $om->getRepository('PurethinkCMSBundle:Site')->getSite();
    }
}