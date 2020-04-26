<?php

namespace App\Controller;

use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonCompteController extends AbstractController
{
    /**
     * @Route("/compte", name="mon_compte")
     */
    public function index(PanierRepository $panierRepository): Response
    {
        $panierFalse = $panierRepository-> findBy(['utilisateur'=> $this -> getUser(), 'etat'=> false]);
        $panierTrue = $panierRepository-> findBy(['utilisateur'=> $this -> getUser(), 'etat'=> true]);
        return $this->render('mon_compte/index.html.twig', [
            'controller_name' => 'MonCompteController',
            'panierTrue' => $panierTrue,
            'panierFalse' => $panierFalse,
        ]);
    }
}
