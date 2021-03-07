<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    #[
        Route(
            '/ma-commande/stripe/create-session/{orderReference}',
            name: 'stripe_create_session',
            methods: [Request::METHOD_POST]
        )
    ]
    public function index(
        Request $request,
        OrderRepository $orderRepository,
        string $orderReference
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        Stripe::setApiKey($this->getParameter('stripe_secret_key'));
        $order = $orderRepository->findOneBy(['reference' => $orderReference]);
        if (!$order) {
            return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
        }

        $items = [];
        foreach ($order->getOrderItems() as $item) {
            /** @var OrderItem $item */
            $items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $item->getPrice(),
                    'product_data' => [
                        'name' => $item->getProductName(),
                        'images' => [$baseurl . '/uploads/' . $item->getPicture()]
                    ]
                ],
                'quantity' => $item->getQuantity()
            ];
        }

        $items[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => 'Frais de livraison ' . $order->getCarrierName(),
                    'images' => [$baseurl]
                ]
            ],
            'quantity' => 1
        ];

        $checkoutSession = Session::create(
            [
                'payment_method_types' => ['card'],
                'customer_email' => $this->getUser()->getEmail(),
                'line_items' => [
                    $items
                ],
                'mode' => 'payment',
                'success_url' =>
                    $this->generateUrl(
                        'order_success',
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ) . '/{CHECKOUT_SESSION_ID}',
                'cancel_url' =>
                    $this->generateUrl(
                        'order_error',
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ) . '/{CHECKOUT_SESSION_ID}'
            ]
        );

        $order->setStripeSessionId($checkoutSession->id);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(
            [
                'id' => $checkoutSession->id
            ]
        );
    }
}
