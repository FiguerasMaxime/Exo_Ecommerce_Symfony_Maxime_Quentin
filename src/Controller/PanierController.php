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
        $panierFind = $panierRepository-> findOneBy(['utilisateur'=> $this -> getUser(), 'etat'=> false]);
        return $this->render('panier/index.html.twig', [
            'panier' => $panierFind,
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

    /**
     * @Route ("panier/buy/{id}", name="status_panier")
     */
    public function buy(Panier $panier, PanierRepository $panierRepository){
        if($panier!=null){
            
            $panier= $panierRepository-> findOneBy(['utilisateur'=> $this -> getUser(), 'etat'=> false]);
            $panier -> setEtat(true);
            $pdo = $this-> getDoctrine()->getManager();
            $pdo -> persist($panier);
            $pdo -> flush();

            $this->addFlash("success", "Panier payé");
        }
        else {
            $this->addFlash("danger", "Panier introuvable");
        }
        return $this -> redirectToRoute('mon_compte');
    }
    
}
