<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CrousController extends AbstractController
{
    #[Route('/restaurants', name: 'liste_restaurants', methods: ['GET'])]
    public function restaurants(): Response
    {
        // http://127.0.0.1/nms/public/restaurants
        header("Location: https://webservices-v2.crous-mobile.fr:8443/crous/crous/regions/18/restaurants");
        die();
    }

    #[Route('/restaurants/{restaurantId}/', name: 'menu_restaurant', methods: ['GET', 'POST'])]
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
