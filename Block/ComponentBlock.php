<?php

namespace Purethink\CMSBundle\Block;

use Doctrine\ORM\EntityManagerInterface;
use Purethink\CoreBundle\Block\AbstractBlock;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class ComponentBlock extends AbstractBlock
{
    const CACHE_TIME = 0;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var TranslatorInterface
     */
    protected $translator;


    /**
     * @param string $name
     * @param EngineInterface $templating
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     */
    public function __construct($name, EngineInterface $templating, EntityManagerInterface $em, RequestStack $requestStack, TranslatorInterface $translator)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => null,
            'slug' => null
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response $response
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $this->translator->setLocale($locale);

        return $this->renderResponse($blockContext->getTemplate(), [
            'entities' => $this->getComponent($locale, $settings['slug']),
            'settings' => $settings
        ],
            $response)->setTtl(self::CACHE_TIME);
    }

    /**
     * @param string $slug
     * @return array
     */
    private function getComponent($locale, $slug)
    {
        if (!$this->em->getFilters()->isEnabled('oneLocale')) {
            $this->em->getFilters()->enable('oneLocale')->setParameter('locale', $locale);
        }

        return $this->em
            ->getRepository('PurethinkCMSBundle:ComponentHasElement')
            ->getActiveComponentBySlugAndLocale($slug, $locale);
    }
}
