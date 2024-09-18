<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CrousController extends AbstractController
{
    #[Route('/api/restaurantsOld', name: 'liste_restaurants_old', methods: ['GET'])]
    public function restaurantsOld(): Response
    {
        // http://127.0.0.1/nms/public/restaurants
//        header("Location: https://webservices-v2.crous-mobile.fr:8443/crous/crous/regions/18/restaurants");
        header("Location: https://mobile-ws.unimes.fr/restaurants.json");
        die();
    }

    #[Route('/api/restaurants', name: 'liste_restaurants', methods: ['GET'])]
    public function restaurants(): JsonResponse
    {
        // http://127.0.0.1/nms/public/restaurants
        // header("Location: https://webservices-v2.crous-mobile.fr:8443/crous/crous/regions/18/restaurants");
        // die();
        $jsonString = file_get_contents('https://webservices-v2.crous-mobile.fr:8443/crous/crous/regions/18/restaurants');
        $data = json_decode($jsonString, true);
        foreach ($data as $key => $entry) {

            foreach (array($entry['opening']) as $entry1) {
		$entry2 = explode (",", $entry1); 
                $opens = [];
              	$jour = 1;
		foreach ($entry2 as $entry3) {
                      if ($entry3 == "000") $opens[$jour] = array("label"=> "Fermé","is_open"=> false);
                      elseif ($entry3 == "001") $opens[$jour] = array("label"=> "Ouvert le soir","is_open"=> true);
                      elseif ($entry3 == "010") $opens[$jour] = array("label"=> "Ouvert le midi","is_open"=> true);
                      elseif ($entry3 == "011") $opens[$jour] = array("label"=> "Ouvert le midi et le soir","is_open"=> true);
                      elseif ($entry3 == "100") $opens[$jour] = array("label"=> "Ouvert le matin","is_open"=> true);
                      elseif ($entry3 == "101") $opens[$jour] = array("label"=> "Ouvert le matin et le soir","is_open"=> true);
                      elseif ($entry3 == "110") $opens[$jour] = array("label"=> "Ouvert le matin et le midi","is_open"=> true);
                      elseif ($entry3 == "111") $opens[$jour] = array("label"=> "Ouvert en continu","is_open"=> true);
                      $jour++;
                }
            }
            $data[$key]['opening'] = $opens;
        }

        // header('Content-Type: application/json; charset=utf-8');
        // echo json_encode($data);
	// exit;
        return $this->json($data);
    }

    #[Route('/api/restaurants/{restaurantId}/', name: 'menu_restaurant', methods: ['GET', 'POST'])]
    public function menus(string $restaurantId): JsonResponse
    {
        // http://127.0.0.1/nms/public/restaurants/1846
        $jsonString = file_get_contents('https://webservices-v2.crous-mobile.fr:8443/crous/crous/regions/19/restaurants/'.$restaurantId.'/menus');
        $data = json_decode($jsonString, true);
        foreach ($data as $key => $entry) {
                $dishes = [];
                foreach ($entry['meal'] as $key1 => $entry1) {
                  foreach ($entry1['foodcategory'] as $key2 => $entry2) {
                      $dishes = [];
                      foreach ($entry2['dishes'] as $key3 => $entry3) {
                        $dishes[] = $entry3['name'];
                      }
                      $data[$key]['meal'][$key1]['foodcategory'][$key2]['dishes'] = $dishes;
                  }
                }
        }
      
        // header('Content-Type: application/json; charset=utf-8');
        // echo json_encode($data);
        return $this->json($data);
    }
}
