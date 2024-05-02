<?php

namespace App\EventListener;

use App\Entity\NotifUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use App\EventListener\NotifEnregistrementRepository;
use Doctrine\ORM\EntityManagerInterface; 
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsEntityListener(event: Events::postPersist , method: 'postPersist', entity: NotifUtilisateur::class)]
class NotifUtilisateurChangedNotifier 
{
    private $messaging;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack)
    {
    }

    public function postPersist (NotifUtilisateur $notifUtilisateur, PostPersistEventArgs  $args): void
    {
    }
}
