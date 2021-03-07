<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Service\CartService;
use App\Service\MailjetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $em)
    {
    }

    #[Route('/ma-commande/merci/{checkoutSession?}', name: 'order_success')]
    public function checkoutSuccess(
        CartService $cart,
        string $checkoutSession,
        MailjetService $mailjet
    ): Response {
        if ($checkoutSession) {
            /** @var Order $order */
            $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($checkoutSession);
            if (!$order || $this->getUser() !== $order->getUser()) {
                return $this->redirectToRoute('cart');
            }

            if ($order->getStatus() === Order::STATUS_NO_VALIDATED) {
                $cart->remove();
                $order->setStatus(Order::STATUS_PAID);
                $this->getDoctrine()->getManager()->flush();

                $mail_content = "Bonjour {$order->getUser()->getFirstName()},<br>
                <p>
                    Merci pour votre commande sur notre boutique !<br><br>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis dolor exercitationem facere, 
                    incidunt ipsum iure labore magni nam nulla optio porro quibusdam 
                    recusandae rem reprehenderit sed ullam ut, vel veniam.
                </p>";
                $mailjet->send(
                    $order->getUser()->getEmail(),
                    $order->getUser()->getFullName(),
                    'Confirmation de commande',
                    $mail_content
                );
            }

            return $this->render(
                'checkout/success.html.twig',
                [
                    'order' => $order
                ]
            );
        }
        return $this->redirectToRoute('cart');
    }

    #[Route('/ma-commande/erreur/{checkoutSession?}', name: 'order_error')]
    public function checkoutError(
        string $checkoutSession
    ): Response {
        if ($checkoutSession) {
            /** @var Order $order */
            $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($checkoutSession);
            if (!$order || $this->getUser() !== $order->getUser()) {
                return $this->redirectToRoute('cart');
            }

            if ($order->getStatus() !== Order::STATUS_PAID_REFUSED) {
                $order->setStatus(Order::STATUS_PAID_REFUSED);
                $this->getDoctrine()->getManager()->flush();
                // Envoyer un email pour indiquer le refus de la transaction
            }

            return $this->render(
                'checkout/error.html.twig',
                [
                    'order' => $order
                ]
            );
        }
        return $this->redirectToRoute('cart');
    }
}
