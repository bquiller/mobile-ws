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
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

#[AsEntityListener(event: Events::postPersist , method: 'postPersist', entity: NotifUtilisateur::class)]
class NotifUtilisateurChangedNotifier 
{
    private $messaging;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack)
    {
	$factory = (new Factory)->withServiceAccount(__DIR__.'/../../config/unimes_credentials.json'); 
        $this->messaging = $factory->createMessaging();
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

        $this->messaging->send($message);
        
        // var_dump($response->getStatusCode());
        // var_dump($response->getBody()->getContents());
      }
    }
}
