<?php

namespace App\Controller;

use \SoapClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use L3\Bundle\LdapUserBundle\Entity\LdapUser;
use OpenLdapObject\Builder\Condition;
use Symfony\Component\Ldap\Ldap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Response,Request};

class HyperPlanningController extends AbstractController
{
    #[Route('/api/edt/{username}', name: 'emploi_du_temps', methods: ['GET'])]
    public function edt(Request $request, EntityManagerInterface $entityManager, UserProviderInterface $ldapUP, string $username): JsonResponse
    {
        // $user = $ldapUP->loadUserByIdentifier($username);
        $ldap = Ldap::create('ext_ldap', ['connection_string' => 'ldap://'.$this->getParameter('ldap_hostname').':389']);
        $query = $ldap->query('ou=people,'.$this->getParameter('ldap_base_dn'), '(&(uid='.$username.'))');
        $ldap->bind($this->getParameter('ldap_dn'), $this->getParameter('ldap_password'));
        $results = $query->execute();
	// date_default_timezone_set('Europe/Paris');

        foreach( $results as $index => $tab ) {
            //print_r($tab);exit;
            if ($tab->getAttribute('supannCodeINE') !== null) $ine = $tab->getAttribute('supannCodeINE')[0];
            if ($tab->getAttribute('supannEmpId') !== null) $supannempid = $tab->getAttribute('supannEmpId')[0];
            $sn = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC')->transliterate(strtoupper($tab->getAttribute('sn')[0]));
            $givenname = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC')->transliterate(strtoupper($tab->getAttribute('givenName')[0]));
        }

        $debut = $request->get('startDate');
        $fin = $request->get('endDate');

        $WSDL = $this->getParameter('hp_wsdl');
        $LOGIN = $this->getParameter('hp_user');
        $PASS = $this->getParameter('hp_password');

        // Creation du client SOAP
        $client = new SoapClient($WSDL, array('login'=> $LOGIN,'password'=> $PASS));
        $events = array();
        
        try {
            $etudiant = $client->AccederEtudiantParNumeroINE($ine);
            $cours = $client->CoursEtudiantEntre2Dates($etudiant, $debut, $fin);
        } catch (\Exception $e) {
            $enseignant = $client->AccederEnseignantParNomPrenomEtCode($sn, $givenname, $supannempid);
            $cours = $client->CoursEnseignantEntre2Dates($enseignant, $debut, $fin);
        }

        if (empty($cours)) exit();

        $cours = $client->RestreindreTableauDeCoursAuxCoursPlaces($cours);
        foreach($cours as $uncours) {
            $seances = $client->DetailDesSeancesPlaceesDuCours($uncours);

            foreach($seances as $uneseance) {
                $memo = $client->MemoCours($uncours);
                $libelle = $client->LibelleMatiere($uneseance->Matiere).' '.$memo;

                $dureeHM = $client->HpSvcWDureeEnHeureMinute($uneseance->Duree);
                $dateFin = strtotime($uneseance->JourEtHeureDebut . ' +'. $dureeHM['AHeure'] .' hour +'.$dureeHM['AMinute'] .' minutes');
                if (time() - strtotime($uneseance->JourEtHeureDebut) > 0 || $dateFin - time() < 0) continue;

                $event = array(
                    "id"=> $uneseance->Matiere,
                    "startDateTime"=>date('Y-m-d\TH:i:s', strtotime($uneseance->JourEtHeureDebut)),
                    "endDateTime"=> date('Y-m-d\TH:i:s', $dateFin),
                    "course"=> array("id"=> $uncours,"label"=>$libelle,"type"=>$uneseance->TypeCours,
                        "color"=>"#ffffff", "type"=>"","online"=>false,"url"=>null),
                    "rooms"=> array(),
                    "teachers"=> array(),
                    "groups"=> array(),
                );

                // var_dump($uneseance);
                foreach ($uneseance->TableauSalle as $salle) {
                    $rooms = array("id"=>$salle, "label"=>$client->LibelleLongSalle($salle),
                        "type"=>"Salle", "building"=>"TEST");
                    array_push($event["rooms"], $rooms);
                }

                foreach ($uneseance->TableauEnseignant as $enseignant) {
                    $teachers = array("id"=>$enseignant, "displayname"=>$client->CiviliteEnseignant($enseignant).' '.$client->NomEnseignant($enseignant),
                        "email"=>$client->EMailEnseignant($enseignant));
                    array_push($event["teachers"], $teachers);
                }

                array_push($events, $event);
            }
        }

        $messages = array("level"=>"INFO","text"=>"Cet emploi du temps dépend de vos inscriptions pédagogiques : vérifiez les !");

        $plannings = array("id"=>"001","label"=>"Planning","default"=>true,"type"=>"USER","messages"=>array($messages),"events"=>$events);

        $reponse = array("messages"=>$messages,"plannings"=>array($plannings));

        return $this->json($reponse);

    }

}


