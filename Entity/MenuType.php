<?php

namespace Purethink\CMSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="menu_type")
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class MenuType
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotNull()
     * @Assert\Length(max="128")
     */
    protected $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="type", orphanRemoval=true)
     */
    protected $menus;

    public function __toString()
    {
        return (string)$this->getName();
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

    /**
     * Set name
     *
     * @param string $name
     * @return MenuType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return MenuType
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    /**
     * Add menu
     *
     * @param Menu $menu
     *
     * @return MenuType
     */
    public function addMenu(Menu $menu)
    {
        $this->menus[] = $menu;

        return $this;
    }

    /**
     * Remove menu
     *
     * @param Menu $menu
     */
    public function removeMenu(Menu $menu)
    {
        $this->menus->removeElement($menu);
    }

    /**
     * Get menus
     *
     * @return Collection
     */
    public function getMenus()
    {
        return $this->menus;
    }
}
