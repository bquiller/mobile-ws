<?php

namespace App\Entity;

use App\Repository\NotifGroupesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifGroupesRepository::class)]
class NotifGroupes {
    
    #[ORM\Id]
    #[ORM\Column(name: 'LL_GROUPE', type: 'string')]
    private $llGroupe;
    
    public function getLlGroupe() {
        return $this->llGroupe;   
    }
    
    public function setLlGroupe($llGroupe) {
        $this->llGroupe = $llGroupe;
        return $this;
    }
    
    public function __toString()
    {
        return $this->getLlGroupe();
    }
}