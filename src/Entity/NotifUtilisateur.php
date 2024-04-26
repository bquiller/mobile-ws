<?php

namespace App\Entity;

use App\Repository\NotifUtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifUtilisateurRepository::class)]
class NotifUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'Notification', )]
    #[ORM\JoinColumn(name:'ID_NOTIFICATION', referencedColumnName:'ID')]
	private $notification;

    #[ORM\Column(length: 100)]
    private ?string $username = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $state = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(Notification $notification): static
    {
        $this->notification = $notification;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->username;
    }

    public function setUserName(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }
    
    public function __toString()
    {
        return $this->getUserName();
    }

}
