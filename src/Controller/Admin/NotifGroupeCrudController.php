<?php

namespace App\Controller\Admin;

use App\Entity\NotifGroupe;
use App\Repository\NotifGroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{TextField,AssociationField};
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class NotifGroupeCrudController extends AbstractCrudController
{
    private NotifGroupeRepository $notifGroupeRepository;

    public static function getEntityFqcn(): string
    {
        return NotifGroupe::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        $new = [
            // TextField::new('groupname','Nom du groupe')->setRequired(true)->autocomplete(),
            // AssociationField::new('groupname','Nom du groupe')->setRequired(true)->autocomplete(),
            AssociationField::new('groupname','Nom du groupe')->setRequired(true),
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
    /*
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
    $ldap = Ldap::create('ext_ldap', ['connection_string' => 'ldap://'.$this->getParameter('ldap_hostname').':389']);
    $query = $ldap->query('ou=people,'.$this->getParameter('ldap_base_dn'), '(&(uid='.$username.'))');
    $ldap->bind($this->getParameter('ldap_dn'), $this->getParameter('ldap_password'));
    $results = $query->execute();
    
        foreach( $results as $index => $tab ) {
            //print_r($tab);exit;
            if ($tab->getAttribute('supannCodeINE') !== null) $ine = $tab->getAttribute('supannCodeINE')[0];
            if ($tab->getAttribute('supannEmpId') !== null) $supannempid = $tab->getAttribute('supannEmpId')[0];
            $sn = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC')->transliterate(strtoupper($tab->getAttribute('sn')[0]));
            $givenname = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC')->transliterate(strtoupper($tab->getAttribute('givenName')[0]));
        }
    }

    */

}

