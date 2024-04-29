<?php

namespace App\Controller;

use App\Repository\NotifUtilisateurRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};

class NotificationController extends AbstractController
{

    #[Route('/channels/{username}', name: 'liste_channels_desabonne', methods: ['GET'])]
    public function channels(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository, string $username): JsonResponse
    {
      $data = array();
      return $this->json($data);
    }

    #[Route('/notifications/{username}', name: 'liste_notifications', methods: ['GET'])]
    public function notifications(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository, string $username): JsonResponse
    {
      $notifications = $notifUtilisateurRepository->findByUsername($username);
      $data = array();
      foreach ($notifications as $key => $notif) {
        $data[]=array(
          "id" => $notif->getId(),
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
    
    #[Route('/notifications', name: 'suppr_notifications', methods: ['DELETE'])]
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

    #[Route('/notifications/read', name: 'read_notif', format:'json', methods: ['POST'])]
    public function read(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository, LoggerInterface $logger): JsonResponse
    {
      $parameters = $request->toArray();
      $username = $parameters['username'];
      $nid = $parameters['notificationIds'][0];

      $notif = $notifUtilisateurRepository->findByUsernameAndNotification($username, $nid);
      $notif->setState("READ");
      $notifUtilisateurRepository->save($notif, true);

      $data = array('OK');
      return $this->json($data);
    }

    #[Route('/notifications/unread', name: 'unread_notif', methods: ['POST'])]
    public function unread(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository): JsonResponse
    {
      $parameters = json_decode($request, true);
      $username = $parameters['username']; 
      $nid = $parameters['notificationId']; 

      
      $notif = $notifUtilisateurRepository->findByUsernameAndNotification($username, $nid);
      $notif->setState("UNREAD");

      $data = array('OK');
      return $this->json($data);
    }

    #[Route('/register', name: 'fcm_register', methods: ['POST'])]
    public function register(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository): JsonResponse
    {
      $parameters = json_decode($request->getContent(), true);
      // $parameters['username']; 
      // $parameters['token']; 
      // $parameters['platform']; 
      // $parameters['ip']; 
      $data = array();
      return $this->json($data);
    }

    #[Route('/unregister', name: 'fcm_unregister', methods: ['POST'])]
    public function unregister(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository): JsonResponse
    {
      $parameters = json_decode($request->getContent(), true);
      // $parameters['username']; 
      // $parameters['token']; 
      $data = array();
      return $this->json($data);
    }
}

