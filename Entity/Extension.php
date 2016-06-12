<?php

namespace Purethink\CMSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="cms_extension")
 * @ORM\Entity(repositoryClass="Purethink\CMSBundle\Repository\ExtensionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Extension implements SoftDeleteable
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Length(max="255")
     */
    private $name;

    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ExtensionHasField", mappedBy="extension", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $fields;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Component", mappedBy="extension", orphanRemoval=true)
     */
    protected $components;

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
     * @return Extension
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
     * Get name
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getName();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->components = new ArrayCollection();
    }

    /**
     * Add fields
     *
     * @param ExtensionHasField $fields
     * @return Extension
     */
    public function addField(ExtensionHasField $fields)
    {
        $fields->setExtension($this);
        $this->fields[] = $fields;

        return $this;
    }

    /**
     * Remove fields
     *
     * @param ExtensionHasField $fields
     */
    public function removeField(ExtensionHasField $fields)
    {
        $this->fields->removeElement($fields);
    }

    /**
     * Get fields
     *
     * @return Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Add component
     *
     * @param Component $component
     *
     * @return Extension
     */
    public function addComponent(Component $component)
    {
        $this->components[] = $component;

        return $this;
    }

    /**
     * Remove component
     *
     * @param Component $component
     */
    public function removeComponent(Component $component)
    {
        $this->components->removeElement($component);
    }

    /**
     * Get components
     *
     * @return Collection
     */
    public function getComponents()
    {
        return $this->components;
    }
}
