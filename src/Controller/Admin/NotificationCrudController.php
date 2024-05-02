<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Repository\{NotificationRepository,NotifUtilisateurRepository,NotifEnregistrementRepository};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action,Actions,Crud};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{TextField,TextareaField,AssociationField,UrlField,CollectionField};
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Kreait\Firebase\Database;
use Kreait\Firebase\ServiceAccount;

class NotificationCrudController extends AbstractCrudController
{
    private NotificationRepository $notificationRepository;

    public static function getEntityFqcn(): string
    {
        return Notification::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        $new = [
            TextField::new('title','Titre')->setRequired(true),
            TextField::new('channel','Canal')->setRequired(true),
            TextareaField::new('message','Message')->setRequired(true),
            
            UrlField::new('lien', 'Lien')->setRequired(false),
            TextField::new('author', 'Auteur')->setRequired(false),

            CollectionField::new('utilisateurs', 'Cibles')->useEntryCrudForm(NotifUtilisateurCrudController::class)->setRequired(false)
        ];
        return $new;
    }

    public function configureActions(Actions $actions): Actions
    {
        $envoiAction = Action::new('send', 'Envoyer la notification', '')
            ->linkToCrudAction('envoiAction')
            ->displayAsLink();
    

        return $actions
            ->add(Crud::PAGE_EDIT, $envoiAction);
    }
    
    public function envoiAction(AdminContext $context, NotifUtilisateurRepository $notifUtilisateurRepository, NotifEnregistrementRepository $notifEnregistrementRepository)
    {
        $notification = $context->getEntity()->getInstance(); 
        
        $factory = (new Factory)->withServiceAccount(__DIR__.'/../../../config/unimes_credentials.json')
        ->withDatabaseUri('https://unimes-campus-default-rtdb.europe-west1.firebasedatabase.app/');

        $cloudMessaging = $factory->createMessaging();
        
        $utilisateurs = $notifUtilisateurRepository->findByNotification($notification);
        
        foreach ($utilisateurs as $key => $utilisateur) {
            $devices = $notifEnregistrementRepository->findByUsername($utilisateur->getUsername());
            foreach ($devices as $key => $device) {
                $message = CloudMessage::withTarget('token', $device->getToken())
                    ->withNotification(FcmNotification::create(
                        $notification->getTitle(), 
                        $notification->getMessage()));
                $cloudMessaging->send($message);
            }
        }
        return $this->redirectToRoute('admin');
  }
}
