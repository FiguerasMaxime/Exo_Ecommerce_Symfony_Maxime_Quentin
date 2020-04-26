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
        $panierFind = $panierRepository-> findOneBy(['utilisateur'=> $this -> getUser(), 'etat'=> false]); // récupération du panier de l'uilisateur connecté si son panier est false
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
            
            $panier= $panierRepository-> findOneBy(['utilisateur'=> $this -> getUser(), 'etat'=> false]);// récupération du panier de l'uilisateur connecté si son panier est false
            $panier -> setEtat(true); // on définit l'état du panier à true pour faire changer son état 
            $pdo = $this-> getDoctrine()->getManager();
            $pdo -> persist($panier); // on l'envoie la data du panier dans la bdd
            $pdo -> flush(); 

            $this->addFlash("success", "Panier payé");
        }
        else {
            $this->addFlash("danger", "Panier introuvable");
        }
        return $this -> redirectToRoute('mon_compte'); // redirection sur la page du compte
    }
    
}
