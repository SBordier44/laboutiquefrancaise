<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Service\MailjetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $em, protected MailjetService $mailjetService)
    {
    }

    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(
        Request $request
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }

        if ($request->isMethod(Request::METHOD_POST) && $request->get('email')) {
            /** @var User $user */
            $user = $this->em->getRepository(User::class)->findOneByEmail($request->get('email'));

            if ($user) {
                $resetPassword = new ResetPassword();
                $resetPassword
                    ->setCreatedAt(new \DateTime())
                    ->setUser($user)
                    ->setToken(rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '='));

                $this->em->persist($resetPassword);
                $this->em->flush();

                $url = $this->generateUrl(
                    'reset_password_update',
                    ['token' => $resetPassword->getToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $mail_content = "Bonjour {$user->getFirstName()},<br>
                    <p>
                        Vous avez demandé à réinitialiser votre mot de passe sur le site de la boutique Francaise.
                    </p>
                    <br>
                    <p>
                        Merci de bien vouloir cliquer sur le lien suivant afin de 
                        <a href='{$url}' target='_blank'>mettre à jour votre mot de passe</a>.
                    </p>
                    <br>
                    <p>
                        Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email.
                    </p>
                ";
                $this->mailjetService->send(
                    $user->getEmail(),
                    $user->getFullName(),
                    'Réinitialisation de votre mot de passe',
                    $mail_content
                );

                $this->addFlash(
                    'success',
                    'Votre demande de réinitialisation de mot de passe à été prise en compte.<br>
                    Vous allez recevoir dans quelques instant un email vous permettant de redéfinir ce dernier.<br>
                    Ce lien de réinitialisation à une validité d\'une heure. 
                    Passé ce délai, il vous faudra recommencer l\'opération.'
                );
            } else {
                $this->addFlash('notice', 'Cette adresse email est inconnue');
            }
        }
        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/reinitialiser-mon-mot-de-passe/{token}', name: 'reset_password_update')]
    public function updatePassword(
        string $token,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }

        /** @var ResetPassword $resetPassword */
        $resetPassword = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$resetPassword) {
            return $this->redirectToRoute('security_login');
        }

        if (new \DateTime() > $resetPassword->getCreatedAt()->modify('+ 1 hour')) {
            $this->addFlash('notice', 'Votre demande de réinitialisation de mot de passe à expiré');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */

            $newPassword = $form->get('new_password')->getData();

            $resetPassword->getUser()->setPassword(
                $passwordEncoder->encodePassword($resetPassword->getUser(), $newPassword)
            );
            $this->em->flush();

            $this->addFlash(
                'success',
                'Votre nouveau mot de passe à été sauvegardé avec succès !<br>
                Vous pouvez à présent vous connecter avec ce dernier.
            '
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'reset_password/update.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
