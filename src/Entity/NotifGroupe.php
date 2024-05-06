<?php

namespace App\Entity;

use App\Repository\NotifGroupeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifGroupeRepository::class)]
class NotifGroupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'Notification', )]
    #[ORM\JoinColumn(name:'ID_NOTIFICATION', referencedColumnName:'ID')]
	private $notification;

    #[ORM\ManyToOne(targetEntity: 'NotifGroupes', )]
    #[ORM\JoinColumn(name:'GROUPNAME', referencedColumnName:'LL_GROUPE')]
    private $groupname = null;

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

    public function getGroupName(): ?NotifGroupes
    {
        return $this->groupname;
    }

    public function setGroupName(NotifGroupes $groupname): static
    {
        $this->groupname = $groupname;

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
        return $this->getGroupName()->getLlGroupe();
    }

}
