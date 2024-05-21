<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use L3\Bundle\LdapUserBundle\Entity\LdapUser;
use OpenLdapObject\Builder\Condition;
use Psr\Log\LoggerInterface;
use Symfony\Component\Ldap\Ldap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Response,Request};

class CarteController extends AbstractController
{
    #[Route('/api/carte/{username}', name: 'carte', methods: ['GET'])]
    public function carte(Request $request, string $username, LoggerInterface $logger): JsonResponse
    {
        if ($request->headers->get('Authorization') !== 'Bearer '.$this->getParameter('bearer_token')) {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }
        
        $ldap = Ldap::create('ext_ldap', ['connection_string' => 'ldap://'.$this->getParameter('ldap_hostname').':389']);
        $query = $ldap->query('ou=people,'.$this->getParameter('ldap_base_dn'), '(&(uid='.$username.'))');
        $ldap->bind($this->getParameter('ldap_dn'), $this->getParameter('ldap_password'));
        $results = $query->execute();
        
        foreach( $results as $index => $tab ) {
            //print_r($tab);exit;
            $nom = $tab->getAttribute('sn')[0];
            $prenom = $tab->getAttribute('givenName')[0];
            $naissance = $tab->getAttribute('dateNaissance')[0];
            $genre = $tab->getAttribute('supannCivilite')[0];
            $affiliation = $tab->getAttribute('eduPersonPrimaryAffiliation')[0];
            $photo = 'data:image/jpeg;base64,'.base64_encode($tab->getAttribute('jpegPhoto')[0]);
            $csn = $tab->getAttribute('unimesCarte')[0];
            $cards = array();
            if (date("m") > 8) $fin = date("Y")+1;
            else $fin = date("Y");

            if ($tab->getAttribute('supannCodeINE') !== null) {
                $title="Carte étudiant";
                $identifiant = $tab->getAttribute('supannCodeINE')[0];
                $model = $this->getParameter('card_student_model');
            } else if ($tab->getAttribute('supannEmpId') !== null) {
                $title="Carte professionnelle";
                $identifiant = $tab->getAttribute('supannEmpId')[0];
                $model = $this->getParameter('card_staff_model');
            }
            $cards = array($model=> array(
                "title"=>$title,
                "subtitle"=>null,
                  "endDate"=>$fin,
                  "idNumber"=>$csn,
                  "csn"=>$csn,
                  "qrCode"=>array(
                      "type"=>"text",
                      "value"=>$username.'@unimes.fr')
                  ) 
            );
        }

        $data = array(
          "lastname"=> $nom,
          "firstname"=> $prenom,
          "birthdate"=> $naissance,
          "gender"=> $genre,
          "affiliation"=> $affiliation,
          "photo"=> $photo,
          "ine"=> $identifiant,
          "errors"=>array(), 
          "cards"=>$cards
        );

        return $this->json($data);

    }

}
