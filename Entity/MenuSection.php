<?php

namespace Purethink\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Purethink\CMSBundle\Repository\MenuSectionRepository")
 * @ORM\Table
 */
class MenuSection extends Menu
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $section;


    public function getTypeOf()
    {
        return self::TYPE_OF_SECTION;
    }

    /**
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param string $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }
}