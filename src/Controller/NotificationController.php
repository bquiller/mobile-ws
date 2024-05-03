<?php

namespace App\Controller;

use App\Entity\NotifEnregistrement;
use App\Repository\{NotifUtilisateurRepository, NotifEnregistrementRepository};
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};


use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Database;
use Kreait\Firebase\ServiceAccount;

class NotificationController extends AbstractController
{

    #[Route('/api/channels/{username}', name: 'liste_channels_desabonne', methods: ['GET'])]
    public function channels(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository, string $username): JsonResponse
    {
      $data = array();
      return $this->json($data);
    }

    #[Route('/api/notifications/{username}', name: 'liste_notifications', methods: ['GET'])]
    public function notifications(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository, string $username): JsonResponse
    {
      //pour supporter ?offset=0&length=10
      $offset = $request->get('offset');
      $length = $request->get('length');

      $notifications = $notifUtilisateurRepository->findByUsername($username, $offset, $length);
      $data = array();
      foreach ($notifications as $key => $notif) {
        $data[]=array(
          "id" => $notif->getNotification()->getId(),
          "author" => $notif->getNotification()->getAuthor(),
          "channel" => $notif->getNotification()->getChannel(),
          "title" => $notif->getNotification()->getTitle(),
          "message" => $notif->getNotification()->getMessage(),
          "state" => $notif->getState(),
          "url" => $notif->getNotification()->getLien(),
          "creationDate" => $notif->getNotification()->getDateCreation(),
        );
      }        
      return $this->json($data);
    }
    
    #[Route('/api/notifications', name: 'suppr_notifications', methods: ['DELETE'])]
    public function supprNotifications(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository): JsonResponse
    {
      $parameters = $request->toArray();
      $username = $parameters['username'];
      $nid = $parameters['notificationId'];

      $notif = $notifUtilisateurRepository->findByUsernameAndNotification($username, $nid);
      $notifUtilisateurRepository->remove($notif, true);

      $data = array('OK');
      return $this->json($data);
    }

    #[Route('/api/notifications/read', name: 'read_notif', format:'json', methods: ['POST'])]
    public function read(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository, LoggerInterface $logger): JsonResponse
    {
      $parameters = $request->toArray();
      $username = $parameters['username'];
      // TODO : support le foreach

      $nid = $parameters['notificationIds'];
      foreach ($nid as $key => $notifId) {
        $notif = $notifUtilisateurRepository->findByUsernameAndNotification($username, $notifId);
        $notif->setState("READ");
        $notifUtilisateurRepository->save($notif, true);
      }

      $data = array('OK');
      return $this->json($data);
    }

    #[Route('/api/notifications/unread', name: 'unread_notif', methods: ['POST'])]
    public function unread(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository): JsonResponse
    {
      $parameters = $request->toArray();
      $username = $parameters['username']; 
      $nid = $parameters['notificationId']; 
      
      $notif = $notifUtilisateurRepository->findByUsernameAndNotification($username, $nid);
      $notif->setState("UNREAD");
      $notifUtilisateurRepository->save($notif, true);

      $data = array('OK');
      return $this->json($data);
    }

    #[Route('/api/register', name: 'fcm_register', methods: ['POST'])]
    public function register(Request $request, NotifEnregistrementRepository $notifEnregistrementRepository): JsonResponse
    {
      $parameters = $request->toArray();
      $enregistrement = new NotifEnregistrement($parameters['username'], $parameters['token'], $parameters['platform'], $parameters['ip']);
      $notifEnregistrementRepository->save($enregistrement,true);
      
      $data = array('OK');
      return $this->json($data);
    }

    #[Route('/api/unregister', name: 'fcm_unregister', methods: ['POST'])]
    public function unregister(Request $request, NotifEnregistrementRepository $notifEnregistrementRepository): JsonResponse
    {
      $parameters = $request->toArray();
      $enregistrement = $notifEnregistrementRepository->findByUsernameAndToken($parameters['username'], $parameters['token']);
      $notifEnregistrementRepository->remove($enregistrement,true);

      $data = array('OK');
      return $this->json($data);
    }
}

