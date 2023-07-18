<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            
            $username = $this->getUser()->getEmail();
            
            $this->addFlash('warning', "vous êtes déja connecté en tant que $username");
            return $this->redirectToRoute('home');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    /**
     * @Route("/passer_en_admin_{id<\d+>}", name="passer_en_admin")
     */
    public function passerEnAdmin($id, Request $request, EntityManagerInterface $manager)
    {
        $secret = "123123aA";

        $form = $this->createForm(AdminType::class);
        $form->handleRequest($request);

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if(!$user)
        {
            throw $this->createNotFoundException("impossible de trouver l'utilisateur avec l'id : $id");
        }

        if($form->isSubmitted() && $form->isValid() )
        {
            if($form->get('secret')->getData() == $secret )
            {
                $user->setRoles(["ROLE_ADMIN"]);
            }else{
                throw $this->createNotFoundException("vous n'avez pas le bon code , êtes-vous un intrus ?");
            }

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('security/passerEnAdmin.html.twig', [
            "user" => $user,
            "formAdmin" => $form->createView()
        ]);
    }


    /**
     * @Route("/confirmation-de-compte_{token}", name="confirmation_compte")
     */
    public function confirmationCompte($token, EntityManagerInterface $manager)
    {
        //on récupere l'utilisateur avec le token passé dans l'url
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(["token" => $token]);

        // on verifie si un user à été trouvé
        if(!$user)
        {
            $this->addFlash("erreur", "Aucun Utilisateur a activer ! veuillez vous connecté si vous avez déja activé votre compte ou vous inscrir dans le cas écheant");

            return $this->redirectToRoute('app_login');
        }

        $user->setToken(null)
            ->setIsVerified(true);

        $manager->persist($user);
        $manager->flush();

        $this->addFlash("success", "Félicitation, votre compte est maintenant activé, vous pouvez vous connecter ");

        return $this->redirectToRoute('app_login');
    } 

}
