<?php

namespace Purethink\CMSBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Purethink\CMSBundle\Entity\Contact;
use Purethink\CMSBundle\Entity\Site;
use Swift_Mime_Message;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

class ContactEmail
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    public function __construct(ContainerInterface $container, EntityManagerInterface $em, \Swift_Mailer $mailer, EngineInterface $templateEngine)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->container = $container;
        $this->templateEngine = $templateEngine;
    }

    private function getSiteRepository()
    {
        return $this->em->getRepository('PurethinkCMSBundle:Site');
    }

    private function getContactEmail()
    {
        /** @var Site $site */
        $site = $this->getSiteRepository()->getSite();
        if (!$site || empty($site->getContactEmail())) {
            throw new EntityNotFoundException;
        }

        return $site->getContactEmail();
    }

    public function sendContactEmailRequest(Contact $contact)
    {
        $body = $this->templateEngine
            ->render('PurethinkCMSBundle:Email:contact.html.twig', compact('contact'));

        /** @var Swift_Mime_Message $message */
        $message = \Swift_Message::newInstance()
            ->setSubject('Contact email')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($this->getContactEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}