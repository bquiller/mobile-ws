<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use L3\Bundle\LdapUserBundle\Entity\LdapUser;
use OpenLdapObject\Builder\Condition;
use Symfony\Component\Ldap\Ldap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Response,Request};

class ProfilController extends AbstractController
{
    #[Route('/api/profil/{username}', name: 'profil', methods: ['GET'])]
    public function profil(Request $request, string $username): JsonResponse
    {
        // $user = $ldapUP->loadUserByIdentifier($username);
        $ldap = Ldap::create('ext_ldap', ['connection_string' => 'ldap://'.$this->getParameter('ldap_hostname').':389']);
        $query = $ldap->query('ou=people,'.$this->getParameter('ldap_base_dn'), '(&(uid='.$username.'))');
        $ldap->bind($this->getParameter('ldap_dn'), $this->getParameter('ldap_password'));
        $results = $query->execute();
        
        foreach( $results as $index => $tab ) {
            //print_r($tab);exit;
            $nom = $tab->getAttribute('sn')[0];
            $prenom = $tab->getAttribute('givenName')[0];
            $nomPrenom = $tab->getAttribute('displayName')[0];
            $mail = $tab->getAttribute('mail')[0];
            $affiliation = $tab->getAttribute('eduPersonPrimaryAffiliation')[0];
        }

        $data = array(
          "displayName"=> $nomPrenom,
          "name"=> $nom,
          "firstname"=> $prenom,
          "email"=> $mail,
          "roles"=> $affiliation
        );

        return $this->json($data);

    }

}
