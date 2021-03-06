<?php

namespace Purethink\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Purethink\CoreBundle\Entity\Category;
use Purethink\CoreBundle\Entity\Gallery;
use Purethink\CoreBundle\Entity\Media;
use Purethink\CoreBundle\Entity\Tag;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use A2lix\I18nDoctrineBundle\Doctrine\ORM\Util\Translatable;

/**
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Purethink\CMSBundle\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Article implements MetadataInterface, ArticleViewInterface, SoftDeleteable
{
    use Translatable;
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $published = true;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Purethink\CoreBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    protected $user;

    /**
     * @var ArticleViewInterface
     *
     * @ORM\OneToOne(targetEntity="ArticleView", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    protected $view;

    /**
     * @var ComponentHasArticle
     *
     * @ORM\OneToMany(targetEntity="ComponentHasArticle", mappedBy="article")
     */
    protected $componentHasArticle;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Purethink\CoreBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $media;

    /**
     * @var Gallery
     *
     * @ORM\ManyToOne(targetEntity="Purethink\CoreBundle\Entity\Gallery", cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $gallery;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Purethink\CoreBundle\Entity\Tag", cascade={"persist"})
     * @ORM\JoinTable(name="article_has_tag",
     *      joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     * @Assert\Valid()
     */
    protected $tags;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Purethink\CoreBundle\Entity\Category", cascade={"persist"})
     */
    protected $category;

    /**
     * @var Article
     *
     * @ORM\OneToMany(targetEntity="Purethink\CMSBundle\Entity\ArticleSource", mappedBy="article", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    protected $sources;

    protected $translations;


    public function getName()
    {
        if ($this->getCurrentTranslation()) {
            return $this->getCurrentTranslation()->getName();
        }

        return null;
    }

    public function getSlug()
    {
        if ($this->getCurrentTranslation()) {
            return $this->getCurrentTranslation()->getSlug();
        }

        return null;
    }

    public function getTitle()
    {
        return $this->getName();
    }

    public function getDescription()
    {
        return $this->getCurrentTranslation()->getDescription();
    }

    public function getKeyword()
    {
        return $this->getCurrentTranslation()->getKeyword();
    }

    public function getViews()
    {
        return $this->getView();
    }

    public function getFirstSource()
    {
        if ($this->getSources()->count() > 0) {
            return $this->getSources()->first();
        }

        return null;
    }

    /**
     * @ORM\PreRemove
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        /** @var ComponentHasArticle $component */
        foreach ($this->getComponentHasArticle() as $component) {
            $element = $component->getComponentHasElement();

            $em = $args->getEntityManager();
            $em->remove($element);
            $em->flush();
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        if ($this->translations && $this->translations->count()) {
            return (string)$this->getName();
        }
        return '';
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * @return Article
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Constructor
     *
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user = null)
    {
        $this->translations = new ArrayCollection();
        $this->componentHasArticle = new ArrayCollection();
        $this->sources = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->setView(new ArticleView());

        if (null != $user) {
            $this->setUser($user);
        }
    }

    /**
     * Add componentHasArticle
     *
     * @param ComponentHasArticle $componentHasArticle
     * @return Article
     */
    public function addComponentHasArticle(ComponentHasArticle $componentHasArticle)
    {
        $this->componentHasArticle[] = $componentHasArticle;

        return $this;
    }

    /**
     * Remove componentHasArticle
     *
     * @param ComponentHasArticle $componentHasArticle
     */
    public function removeComponentHasArticle(ComponentHasArticle $componentHasArticle)
    {
        $this->componentHasArticle->removeElement($componentHasArticle);
    }

    /**
     * Get componentHasArticle
     *
     * @return Collection
     */
    public function getComponentHasArticle()
    {
        return $this->componentHasArticle;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Article
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set view
     *
     * @param ArticleViewInterface $view
     * @return Article
     */
    public function setView(ArticleViewInterface $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get view
     *
     * @return ArticleViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * Add tag
     *
     * @param Tag $tag
     *
     * @return Article
     */
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param Media $media
     * @return Article
     */
    public function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Article
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Add source
     *
     * @param ArticleSource $source
     *
     * @return Article
     */
    public function addSource(ArticleSource $source)
    {
        $source->setArticle($this);
        $this->sources[] = $source;

        return $this;
    }

    /**
     * Remove source
     *
     * @param ArticleSource $source
     */
    public function removeSource(ArticleSource $source)
    {
        $this->sources->removeElement($source);
    }

    /**
     * Get sources
     *
     * @return Collection
     */
    public function getSources()
    {
        return $this->sources;
    }
}
