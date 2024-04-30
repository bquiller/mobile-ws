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
use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;

#[AsEntityListener(event: Events::postPersist , method: 'postPersist', entity: NotifUtilisateur::class)]
class NotifUtilisateurChangedNotifier 
{
    private $server_key;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack)
    {
        $server_key = $_ENV['FIREBASE_KEY'];
    }

    public function postPersist (NotifUtilisateur $notifUtilisateur, PostPersistEventArgs  $event, 
        NotifEnregistrementRepository $notifEnregistrementRepository): void
    {
        $client = new Client();
        $client->setApiKey($server_key);
        
        $message = new Message();
        $message->setPriority('high');

        $devices = $notifEnregistrementRepository->findByUsername($notifUtilisateur->getUsername());

        
      foreach ($devices as $key => $device) {
        $message->addRecipient(new Device($device->getToken()));
        $message->setNotification(
            new Notification($notifUtilisateur->getNotification()->getTitle(), 
                             $notifUtilisateur->getNotification()->getMessage()));
        
        $response = $client->send($message);
        // var_dump($response->getStatusCode());
        // var_dump($response->getBody()->getContents());
      }
    }
}
