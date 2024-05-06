<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifGroupesRepository::class)]
class NotifGroupesUtilisateurs {
    
    #[ORM\Id]
    #[ORM\Column(name:'ID', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private $llGroupe;
    
    
    #[ORM\Column(length: 8)]
    private $cptLogin;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getLlGroupe() {
        return $this->llGroupe;   
    }
    
    public function setLlGroupe($llGroupe) {
        $this->llGroupe = $llGroupe;
        return $this;
    }
    
    public function getCptLogin() {
        return $this->cptLogin;   
    }
    
    public function setCptLogin($cptLogin) {
        $this->cptLogin = $cptLogin;
        return $this;
    }
}