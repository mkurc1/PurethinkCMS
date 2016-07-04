<?php

namespace Purethink\CMSBundle\Entity;

use A2lix\I18nDoctrineBundle\Doctrine\Interfaces\OneLocaleInterface;
use A2lix\I18nDoctrineBundle\Doctrine\ORM\Util\Translation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\SoftDeleteable\SoftDeleteable;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity
 * @ORM\Table(name="component_translation")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class ComponentTranslation implements SoftDeleteable, OneLocaleInterface
{
    use Translation;
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     * @Assert\NotNull()
     * @Assert\Length(min="2", max="128")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2000, nullable=true)
     * @Assert\Length(max="2000")
     */
    protected $description;


    public function __toString()
    {
        return (string)$this->getName();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return MenuTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}