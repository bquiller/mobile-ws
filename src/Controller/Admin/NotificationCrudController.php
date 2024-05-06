<?php

namespace App\Controller\Admin;

use App\Entity\{Notification,NotifUtilisateur};
use App\Repository\{NotificationRepository,NotifUtilisateurRepository,NotifGroupeRepository,NotifEnregistrementRepository,NotifGroupesUtilisateursRepository};
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

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
        ];

        $edit = [
            CollectionField::new('groupes', 'Groupes cibles')->useEntryCrudForm(NotifGroupeCrudController::class)->setRequired(false),
            CollectionField::new('utilisateurs', 'Utilisateurs cibles')->useEntryCrudForm(NotifUtilisateurCrudController::class)->setRequired(false)
        ];

        if(Crud::PAGE_EDIT === $pageName) {
            return array_merge($new, $edit);
        } 

        return $new;
    }

    public function configureActions(Actions $actions): Actions
    {
        $affectAction = Action::new('affect', '2 - Répartir les utilisateurs', '')
            ->linkToCrudAction('affectAction')
            ->displayAsLink();
    
        $envoiAction = Action::new('send', '3 - Envoyer la notification', '')
            ->linkToCrudAction('envoiAction')
            ->displayAsLink();
    
        return $actions
            ->add(Crud::PAGE_EDIT, $affectAction)
            ->add(Crud::PAGE_EDIT, $envoiAction)      
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, fn (Action $action) => $action->setLabel("1 - Enregistrer (contenus, groupes, utilisateurs)"))  
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, fn (Action $action) => $action->setLabel("Sauvegarder et quitter"))  
            ->reorder(Crud::PAGE_EDIT, [Action::SAVE_AND_CONTINUE, 'affect', 'send', Action::SAVE_AND_RETURN])
            ;
    }
    
    public function affectAction(AdminContext $context, NotifGroupeRepository $notifGroupeRepository, 
        NotificationRepository $notificationRepository, 
        NotifEnregistrementRepository $notifEnregistrementRepository,
        NotifGroupesUtilisateursRepository $notifGroupesUtilisateursRepository)
    {
        $notification = $context->getEntity()->getInstance(); 
        
        // pour chaque groupe de la notification
        $groupes = $notifGroupeRepository->findByNotification($notification);

        foreach ($groupes as $key => $groupe) {
            // récupérer les utilisateurs d'un groupe 
            $utilisateurs = $notifGroupesUtilisateursRepository->findByLlGroupe($groupe->getGroupName());
            
            // pour chaque utilisateur
            foreach ($utilisateurs as $key => $utilisateur) {
                $devices = $notifEnregistrementRepository->findByUsername($utilisateur->getCptLogin());
                // et pour chaque device de l'utilisateur
                foreach ($devices as $key => $device) {
                    // créer la notifUtilisateur correspondante
                    $user = new NotifUtilisateur();
                    $user->setUsername($utilisateur->getCptLogin());
                    $user->setState("UNREAD");
                    $notification->addUtilisateur($user);
                    $notificationRepository->save($notification,true);
                }
            }
        }
        
        // On reste sur la page (bizarre qu'il n'y ait as plus simple pour le faire ...)
        $url = $this->container->get(AdminUrlGenerator::class)->setAction(Action::EDIT)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())->generateUrl();
        return $this->redirect($url);
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
