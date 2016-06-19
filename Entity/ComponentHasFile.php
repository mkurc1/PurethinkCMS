<?php

namespace Purethink\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Purethink\CoreBundle\Entity\Media;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class ComponentHasFile extends ComponentHasValue
{
    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Purethink\CoreBundle\Entity\Media", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $file;


    public function setContent($content)
    {
        $this->setFile($content);

        return $this;
    }

    public function getContent()
    {
        return $this->getFile();
    }

    public function getStringContent()
    {
        if ($this->getContent()) {
            return $this->getContent();
        } else {
            return '';
        }
    }

    /**
     * Set file
     *
     * @param Media $file
     * @return ComponentHasFile
     */
    public function setFile(Media $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return Media
     */
    public function getFile()
    {
        return $this->file;
    }
}
