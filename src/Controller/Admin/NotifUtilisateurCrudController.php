<?php

namespace App\Controller\Admin;

use App\Entity\NotifUtilisateur;
use App\Repository\NotifUtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{TextField,ChoiceField};
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class NotifUtilisateurCrudController extends AbstractCrudController
{
    private NotifUtilisateurRepository $notifUtilisateurRepository;

    public static function getEntityFqcn(): string
    {
        return NotifUtilisateur::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        $new = [
            TextField::new('username','Nom d\'utilisateur')->setRequired(true),
            /*
            ChoiceField::new('state')
            ->setChoices([
                'non lu' => 'UNREAD',
                'lu' => 'READ',
            ])->renderExpanded()->setRequired(true),
            */
        ];
        return $new;
    }

}
