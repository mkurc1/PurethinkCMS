<?php

namespace Purethink\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Purethink\CMSBundle\Repository\MenuArticleRepository")
 * @ORM\Table
 */
class MenuArticle extends Menu
{
    /**
     * @var Article
     *
     * @ORM\ManyToOne(targetEntity="Article")
     * @Assert\NotNull()
     */
    protected $article;


    public function getTypeOf()
    {
        return self::TYPE_OF_ARTICLE;
    }
    
    /**
     * @return mixed
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param mixed $article
     * @return MenuArticle
     */
    public function setArticle($article)
    {
        $this->article = $article;
        return $this;
    }
}