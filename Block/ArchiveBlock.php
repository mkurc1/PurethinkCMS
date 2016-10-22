<?php

namespace Purethink\CMSBundle\Block;

use Doctrine\ORM\EntityManagerInterface;
use Purethink\CMSBundle\Repository\ArticleRepository;
use Purethink\CoreBundle\Block\AbstractBlock;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArchiveBlock extends AbstractBlock
{
    const CACHE_TIME = 0;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param string                 $name
     * @param EngineInterface        $templating
     * @param EntityManagerInterface $em
     */
    public function __construct($name, EngineInterface $templating, EntityManagerInterface $em)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => null
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
            'archive' => $this->getArchive()
        ], $response)->setTtl(self::CACHE_TIME);
    }

    private function getArchive()
    {
        return $this->getArticleRepository()
            ->getMonthsWithArticles();
    }

    private function getArticleRepository() : ArticleRepository
    {
        return $this->em->getRepository('PurethinkCMSBundle:Article');
    }
}