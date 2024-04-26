<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{TextField,TextEditorField,AssociationField,UrlField};
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

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
            TextEditorField::new('message','Message')->setRequired(true),
            
            UrlField::new('lien', 'Lien')->setRequired(false),
            TextField::new('author', 'Auteur')->setRequired(false),

            CollectionField::new('utilisateurs', 'Cibles')->useEntryCrudForm(NotifUtilisateurCrudController::class)->setRequired(false)
        ];
        return $new;
    }
}
