<?php

namespace App\Controller;

use \SoapClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Response,Request};

class HyperPlanningController extends AbstractController
{
    #[Route('/edt', name: 'emploi_du_temps', methods: ['GET'])]
    public function edt(Request $request, EntityManagerInterface $entityManager): Response
    {
      $login = 'bquiller';
// $debut = $_GET['startDate'];
// $fin = $_GET['endDate'];

      $attributes = $this->container->get('security.token_storage')->getToken()->getAttributes();
      $ine = "test";
      if (isset($attributes['supannCodeINE'])) 
        $ine = $attributes['supannCodeINE'];
      $sn = $attributes["sn"];
      $gn = $attributes["givenName"];
      $supannempid = 19673;

      date_default_timezone_set('Europe/Paris');

      // URL du document WSDL de HYPERPLANNING Service web
      $WSDL = "http://172.16.15.37/hpsw/wsdl/RpcEncoded";
      // L'identifiant et le mot de passe
      $LOGIN = "bquiller";
      $PASS = "4YJBJ7Cj";

      // Creation du client SOAP
      $client = new SoapClient($WSDL, array('login'=> $LOGIN,'password'=> $PASS));
      $events = array();
      
      try {
        $etudiant = $client->AccederEtudiantParNumeroINE($ine);
        $cours = $client->CoursEtudiantEntre2Dates($etudiant, $debut, $fin);
      } catch (Exception $e) {
        $enseignant = $client->AccederEnseignantParNomPrenomEtCode($sn, $gn, $supannempid);
        $cours = $client->CoursEnseignantEntre2Dates($enseignant, $debut, $fin);
      }

      if (empty($cours)) exit();

      $cours = $client->RestreindreTableauDeCoursAuxCoursPlaces($cours);
      foreach($cours as $uncours) {
          $seances = $client->DetailDesSeancesPlaceesDuCours($uncours);

          foreach($seances as $uneseance) {
              $dureeHM = $client->HpSvcWDureeEnHeureMinute($uneseance->Duree);
              $dateFin = strtotime($uneseance->JourEtHeureDebut . ' +'. $dureeHM['AHeure'] .' hour +'.$dureeHM['AMinute'] .' minutes');
              if (time() - strtotime($uneseance->JourEtHeureDebut) > 0 || $dateFin - time() < 0) continue;

              $event = array(
                  "id"=> $uneseance->Matiere,
                  "startDateTime"=>date('Y-m-d\TH:i:sP', strtotime($uneseance->JourEtHeureDebut)),
                  "endDateTime"=> date('Y-m-d\TH:i:sP', $dateFin),
                  "course"=> array("id"=> $uncours,"label"=>$client->LibelleMatiere($uneseance->Matiere),"type"=>$uneseance->TypeCours,
                      "color"=>"#ffffff", "type"=>"","online"=>false,"url"=>null),
                  "rooms"=> array(),
                  "teachers"=> array(),
                  "groups"=> array(),
              );

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

      $plannings = array("id"=>"001","label"=>"test","default"=>true,"type"=>"USER","messages"=>array($messages),"events"=>$events);

      $reponse = array("messages"=>$messages,"plannings"=>array($plannings));

      echo json_encode($reponse);

    }

}
