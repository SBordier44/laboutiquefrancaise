<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    #[Route('/mon-compte/mon-mot-de-passe', name: 'account_password')]
    public function index(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(ChangePasswordType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $new_password = $form->get('new_password')->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $new_password));
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour !');
            return $this->redirectToRoute('account');
        }

        return $this->render(
            'account/password.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
