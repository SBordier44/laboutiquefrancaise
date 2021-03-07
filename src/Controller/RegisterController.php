<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\MailjetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'register')]
    public function index(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        MailjetService $mailjet
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre inscription à été effectuée avec succès. Vous pouvez à présent vous connecter'
            );

            $mail_content = "Bonjour {$user->getFirstName()},<br>
                <p>
                    Bienvenue sur la première boutique dédée au \"made in France\". <br><br>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis dolor exercitationem facere, 
                    incidunt ipsum iure labore magni nam nulla optio porro quibusdam 
                    recusandae rem reprehenderit sed ullam ut, vel veniam.
                </p>";
            $mailjet->send(
                $user->getEmail(),
                $user->getFullName(),
                'Bienvenue sur la boutique francaise',
                $mail_content
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'register/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
