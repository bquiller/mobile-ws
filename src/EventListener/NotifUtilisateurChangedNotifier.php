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
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

#[AsEntityListener(event: Events::postPersist , method: 'postPersist', entity: NotifUtilisateur::class)]
class NotifUtilisateurChangedNotifier 
{
    public function __construct(Messaging $my_projectMessaging,
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack)
    {
        $this->messaging = $messaging;
    }

    public function postPersist (NotifUtilisateur $notifUtilisateur, PostPersistEventArgs  $args): void
    {
      // $client->setApiKey($_ENV['FIREBASE_KEY']);

      $em = $args->getEntityManager();
      $notifEnregistrementRepository = $em->getRepository('App\Entity\NotifEnregistrement');
      $devices = $notifEnregistrementRepository->findByUsername($notifUtilisateur->getUsername());
        
      foreach ($devices as $key => $device) {
        $message = CloudMessage::withTarget('token', $device->getToken())
            ->withNotification(Notification::create(
                $notifUtilisateur->getNotification()->getTitle(), 
                $notifUtilisateur->getNotification()->getMessage()));

        $messaging->send($message);
        
        // var_dump($response->getStatusCode());
        // var_dump($response->getBody()->getContents());
      }
    }
}
