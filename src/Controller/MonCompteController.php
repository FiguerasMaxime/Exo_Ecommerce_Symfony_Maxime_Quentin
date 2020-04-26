<?php

namespace App\Controller;

use App\Repository\PanierRepository;
use App\Repository\UserRepository;
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

    /**
     * @Route("/compte/admin", name="mon_compte_admin")
     */
    public function super_admin(UserRepository $userRepository, PanierRepository $panierRepository): Response
    {
        // méthode créé dans le répository user afin de pouvoir récupere par date
        $allUser = $userRepository-> findByDate();

        // méthode utiliser pour récupere par état
        $allPaniers = $panierRepository-> findBy(['etat'=> false]);

        return $this->render('mon_compte/admin.html.twig', [
            'controller_name' => 'MonCompteController',
            'users' => $allUser,
            'paniers' => $allPaniers
        ]);
    }
}
