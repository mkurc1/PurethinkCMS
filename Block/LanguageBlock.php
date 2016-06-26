<?php

namespace Purethink\CMSBundle\Block;

use Purethink\CMSBundle\Service\Language;
use Purethink\CoreBundle\Block\AbstractBlock;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageBlock extends AbstractBlock
{
    const CACHE_TIME = 0;

    private $language;

    public function __construct($name, EngineInterface $templating, Language $language)
    {
        parent::__construct($name, $templating);

        $this->language = $language;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'PurethinkCMSBundle:Block:language.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse($blockContext->getTemplate(), [
                'languages' => $this->language->getPublicLanguages()
            ],
            $response)->setTtl(self::CACHE_TIME);
    }
}