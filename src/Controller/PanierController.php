<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\User;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier/", name="panier")
     */
    public function index(PanierRepository $panierRepository): Response
    {

        return $this->render('panier/index.html.twig', [
            'panier' => $panierRepository->findAll(),
        ]);

    }

    /**
     * @Route("/panier/{id}", name="panier_user")
     */
    public function panierUser(Panier $panier, User $user)
    {


        return $this->render('panier/panier.html.twig', [
            'panier' => $panier,
        ]);
    }
    
}
