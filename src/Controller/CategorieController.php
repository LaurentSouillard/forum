<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categorie_ajout', name: 'categorie_ajout')]
    public function ajout(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {

        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $slug = $slugger->slug($categorie->getLabel());

            $categorie->setSlug($slug);

            $manager->persist($categorie);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('categorie/formulaire.html.twig', [
            'formCategorie' => $form->createView()
        ]);
    }
}
