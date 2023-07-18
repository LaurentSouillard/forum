<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $urlGenerator): Response
    {
        if ( $this->getUser()) {
            
            $username = $this->getUser()->getEmail();
            
            $this->addFlash('warning', "vous êtes déja inscrit en tant que $username");
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //on génere un token
            $token = $tokenGenerator->generateToken();

            $user->setDateInscription(new DateTime("now"))
                 ->setIsVerified(false)
                 ->setToken($token)
            ;

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            $url = $urlGenerator->generate('confirmation_compte', ["token" => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = new Email();

            $email->from("lolo.souillard@gmail.com") // email de l'éxpéditeur
                  ->to($user->getEmail()) //email du destinataire
                  ->subject("Inscription au Blog")
                  ->html("<p>Merci de vous etre insrit sur mon <strong>Blog</strong> ! Veuiilez à présent cliquer sur le lien suivant afin d'activer votre compte : <a href=\"$url\">Activer mon compte</a></p>");

            $mailer->send($email);

            $this->addFlash("success", "Inscription validé ! veuillez actier votre compte depuis vos emails.");

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
