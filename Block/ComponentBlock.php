<?php

namespace Purethink\CMSBundle\Block;

use Doctrine\ORM\EntityManagerInterface;
use Purethink\CoreBundle\Block\AbstractBlock;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComponentBlock extends AbstractBlock
{
    const CACHE_TIME = 0;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * @param string                 $name
     * @param EngineInterface        $templating
     * @param EntityManagerInterface $em
     * @param RequestStack           $requestStack
     */
    public function __construct($name, EngineInterface $templating, EntityManagerInterface $em, RequestStack $requestStack)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => null,
            'slug'     => null
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response              $response
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse($blockContext->getTemplate(), [
            'entities' => $this->getComponent($blockContext->getSetting('slug'))
        ],
            $response)->setTtl(self::CACHE_TIME);
    }

    /**
     * @param string $slug
     * @return array
     */
    private function getComponent($slug)
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->em
            ->getRepository('PurethinkCMSBundle:ComponentHasElement')
            ->getActiveComponentBySlugAndLocale($slug, $locale);
    }
}