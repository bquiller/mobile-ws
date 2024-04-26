<?php

namespace App\Entity;

use App\Entity\Behavior\TimestampableTrait;
use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    // Pour la date de création et la date de modification 
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'ID', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column(length: 100)]
    private ?string $channel = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(length: 100)]
    private ?string $lien = null;
    
    #[ORM\Column(length: 100)]
    private ?string $author = null;

    #[ORM\OneToMany(targetEntity: "NotifUtilisateur", fetch: "LAZY", mappedBy: "notification", cascade:['persist'])]
	private $utilisateurs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): static
    {
        $this->channel = $channel;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
    
    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): static
    {
        $this->lien = $lien;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getUtilisateurs()
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(NotifUtilisateur $utilisateur)
    {
        $utilisateur->setNotification($this);
        $utilisateur->setState("UNREAD");
        $this->utilisateurs->add($utilisateur);
        return $this;
    }
    
    public function removeUtilisateur(NotifUtilisateur $utilisateur)
    {
        $this->utilisateurs->removeElement($utilisateur);
        return $this;
    }

}
