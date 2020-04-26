<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContenuPanierController extends AbstractController
{
    /**
     * @Route("/contenu/panier", name="contenu_panier")
     */
    public function index()
    {
        return $this->render('contenu_panier/index.html.twig', [
            'controller_name' => 'ContenuPanierController',
        ]);
    }

    /**
     * @Route ("panier/delete/{id}", name="delete_panier")
     */
    public function delete(ContenuPanier $contenuPanier=null){
        if($contenuPanier!=null){

            $pdo = $this-> getDoctrine()->getManager();
            $pdo -> remove($contenuPanier);
            $pdo -> flush();

            $this->addFlash("success", "Produit supprimé");
        }
        else {
            $this->addFlash("danger", "Produit introuvable");
        }
        return $this -> redirectToRoute('produit_index');
    }
    
}
