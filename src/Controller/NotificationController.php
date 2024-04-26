<?php

namespace App\Controller;

use App\Repository\NotifUtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};

class NotificationController extends AbstractController
{
    #[Route('/notifications/{username}', name: 'liste_notifications', methods: ['GET'])]
    public function restaurants(Request $request, NotifUtilisateurRepository $notifUtilisateurRepository, string $username): JsonResponse
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
      /*
      id: String,             // id de la notif en base
      author: String,         // auteur de la notif (Nom prénom). Default = '@system'
      channel: String,        // canal de diffusion
      title: String,          // titre de la notification
      message: String,        // message de la notification
      state: String,          // état de la notification (READ, UNREAD)
      url: String,           // lien de la page externe vers laquelle rediriger le user
      creationDate: Datetime, // date de la notification
      */
      
        
      return $this->json($data);
    }

}
