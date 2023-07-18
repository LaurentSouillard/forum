<?php

namespace App\Controller;

use DateTime;
use App\Entity\Sujet;
use App\Form\SujetType;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SujetController extends AbstractController
{
    #[Route('/sujet_ajout', name: 'sujet_ajout')]
    public function ajout(Request $request, EntityManagerInterface $manager): Response
    {

        $sujet = new Sujet();

        $form = $this->createForm(SujetType::class, $sujet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $sujet->setDateDeCreation(new DateTime('now'));

            $manager->persist($sujet);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('sujet/formulaire.html.twig', [
            'formSujet' => $form->createView()
        ]);
    }

    #[Route('/forum', name: 'forum')]
    public function forum(ManagerRegistry $doctrine)
    {
        $sujets = $doctrine->getManager()->getRepository(Sujet::class)->findAll();

        $categories = $doctrine->getManager()->getRepository(Categorie::class)->findAll();

        return $this->render('forum/index.html.twig', [
            'sujets' => $sujets,
            'categories' => $categories
        ]);
    }
}
