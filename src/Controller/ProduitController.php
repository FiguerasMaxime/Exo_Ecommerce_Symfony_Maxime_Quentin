<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\User;
use App\Form\ContenuPanierType;
use App\Form\ProduitType;
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

        $entityManager = $this->getDoctrine()->getManager();
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fichier = $form->get('photo')->getData();

            if($fichier){
                $nomFichier = uniqid() .'.'. $fichier->guessExtension();

                try {
                    $fichier->move(
                        $this->getParameter('upload_dir'),
                        $nomFichier
                    );
                }
                catch(FileException $e) {
                    $this->addFlash("danger", "Votre image n'est pas bonne");
                    return $this->redirectToRoute('produit_index');
                }

                $produit->setPhoto($nomFichier);
            }
            $entityManager->persist($produit);
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
    public function show(Produit $produit, Request $request, User $user): Response
    {
        if($produit != null){

            $pdo = $this->getDoctrine()->getManager();
            $produitClass = $pdo->getRepository(Produit::class)->findByid($produit);

            $contenuPanier = new ContenuPanier($produit);
            $contenuPanier -> setProduit($produitClass);

            $panier = new Panier($user);
            $panier -> addUtilisateur($user);
            $panier -> setContenuPanier($contenuPanier);

            $form = $this->createForm(ContenuPanierType::class, $contenuPanier);
            $form->handleRequest($request);

            // formulaire envoyer
            if($form->isSubmitted() && $form->isValid()){
                $pdo->persist($panier);
                $pdo->persist($contenuPanier);
    
                $pdo->flush();

                $this->addFlash("success", "Produit ajouté au panier");
                return $this -> redirectToRoute('panier');
            }
            return $this -> render('produit/show.html.twig', [
                'produit' => $produit,
                'formContenuPanier' => $form->createView()
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
