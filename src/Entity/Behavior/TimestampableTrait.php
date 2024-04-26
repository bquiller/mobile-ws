<?php

namespace App\Entity\Behavior;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait TimestampableTrait
 */
trait TimestampableTrait
{
     #[Gedmo\Timestampable(on:"create")]
     #[ORM\Column(name:'date_creation', type: 'datetime')]
    protected $dateCreation;

    /**
     * Get the value of dateCreation
     */ 
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set the value of dateCreation
     *
     * @return  self
     */ 
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
}