<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(User $user)
    {
        // $panier = $user-> getPanier();
        return $this->render('panier/index.html.twig', [
            // 'panier' => $panier,
            'user' => $user
        ]);
    }
    
}
