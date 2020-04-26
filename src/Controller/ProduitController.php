<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\User;
use App\Form\ContenuPanierType;
use App\Form\ProduitType;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index")
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/new", name="produit_new")
     */
    public function new(Request $request): Response
    {

        $entityManager = $this->getDoctrine()->getManager(); //Récupératio doctrine pour intéragir avec la bdd
        $produit = new Produit(); // création d'un objet produit 
        $form = $this->createForm(ProduitType::class, $produit); // création d'un form de type ProduitType par le produit
        $form->handleRequest($request); // vérification d'une requête (action) effectué sur le form

        if ($form->isSubmitted() && $form->isValid()) { // vérification si le form est envoyé et valide

            $fichier = $form->get('photo')->getData(); 

            if($fichier){
                $nomFichier = uniqid() .'.'. $fichier->guessExtension(); // création d'un id unique à chaque image 

                try {
                    $fichier->move( //envoie de l'image dans le fichier uploads
                        $this->getParameter('upload_dir'),
                        $nomFichier
                    );
                }
                catch(FileException $e) { // si l'image n'est pas bonne, taille d'images, etc...
                    $this->addFlash("danger", "Votre image n'est pas bonne");
                    return $this->redirectToRoute('produit_index'); // redirection à la page d'accueil
                }

                $produit->setPhoto($nomFichier); // insertion du nom de l'image dans le produit
            }
            $entityManager->persist($produit); // envoie du produit 
            $entityManager->flush(); 

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/{id}", name="produit_show")
     */
    public function show(Produit $produit, Request $request, User $user, $id, PanierRepository $panierRepository): Response
    {
        if($produit != null){

            $pdo = $this->getDoctrine()->getManager();
            $produitClass = $pdo->getRepository(Produit::class)->find($id); // récupération des produit par leur id 
            $panier = $panierRepository-> findOneBy(['utilisateur'=> $this -> getUser(), 'etat'=> false]); // récupération du panier de l'uilisateur connecté si son panier est false
            if($panier === null) {
                $panier = new Panier(); // création d'un panier 
                $panier -> setUtilisateur($user); //attribution à l'utilisateur connecté
            }
            $contenuPanier = new ContenuPanier();
            $contenuPanier -> setProduit($produitClass); // attribution des produits dans le contenu de panier
            $contenuPanier-> setPanier($panier); // attribution dans le panier du contenu panier

            $form = $this->createForm(ContenuPanierType::class, $contenuPanier);
            $form->handleRequest($request);

            // formulaire envoyer
            if($form->isSubmitted() && $form->isValid()){
                $pdo->persist($panier);
                $pdo->persist($contenuPanier);
    
                $pdo->flush();

                $this->addFlash("success", "Produit ajouté au panier"); // affichage message réussi
                return $this -> redirectToRoute('panier');
            }
            return $this -> render('produit/show.html.twig', [
                'produit' => $produit,
                'formContenuPanier' => $form->createView(),
                'user' => $user->getId(), // récupération de l'id dans user 
                'panier' => $panier
            ]);
        }
        else{
            //le produit n'existe pas, on redirige l'alternaute
            return $this -> redirectToRoute('produit');
        }
    }


    /**
     * @Route("/produit/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }
}
