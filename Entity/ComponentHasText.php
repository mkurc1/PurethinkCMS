<?php

namespace Purethink\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class ComponentHasText extends ComponentHasValue
{
    public function setContent($content)
    {
        $this->getCurrentTranslation()->setText($content);

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getCurrentTranslation()->getText();
    }

    public function getStringContent()
    {
        return $this->getContent();
    }
}
