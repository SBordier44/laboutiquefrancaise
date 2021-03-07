<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Carrier;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\OrderType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/ma-commande', name: 'order')]
    public function index(
        CartService $cart
    ): Response {
        if (empty($this->getUser()->getAddresses()->getValues())) {
            return $this->redirectToRoute('account_address_create', ['referer' => 'order']);
        }

        if (empty($cart->get())) {
            return $this->redirectToRoute('products');
        }

        $form = $this->createForm(
            OrderType::class,
            null,
            [
                'user' => $this->getUser()
            ]
        );

        return $this->render(
            'order/index.html.twig',
            [
                'form' => $form->createView(),
                'cart' => $cart->get()
            ]
        );
    }

    #[Route('/ma-commande/recapitulatif-de-ma-commande', name: 'order_summary', methods: [Request::METHOD_POST])]
    public function summary(
        Request $request,
        CartService $cart,
        EntityManagerInterface $em
    ): Response {
        if (empty($cart->get())) {
            return $this->redirectToRoute('products');
        }
        $form = $this->createForm(
            OrderType::class,
            null,
            [
                'user' => $this->getUser()
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Carrier $carrier */
            $carrier = $form->get('carriers')->getData();
            /** @var Address $delivery */
            $delivery = $form->get('addresses')->getData();

            $order = new Order();
            $order
                ->setUser($this->getUser())
                ->setReference(date('Ymd') . time() . random_int(1000, 9999))
                ->setCreatedAt(new \DateTime())
                ->setCarrierName($carrier->getName())
                ->setCarrierPrice($carrier->getPrice())
                ->setDelivery($delivery->getDeliveryCard())
                ->setStatus(Order::STATUS_NO_VALIDATED);

            $em->persist($order);

            foreach ($cart->get() as $item) {
                $orderItem = new OrderItem();
                $orderItem
                    ->setParentOrder($order)
                    ->setPrice($item['product']->getPrice())
                    ->setProductName($item['product']->getName())
                    ->setQuantity($item['quantity'])
                    ->setTotal($item['product']->getPrice() * $item['quantity'])
                    ->setPicture($item['product']->getPicture());

                $em->persist($orderItem);
            }

            $em->flush();

            return $this->render(
                'order/summary.html.twig',
                [
                    'cart' => $cart->get(),
                    'carrier' => $carrier,
                    'delivery' => $delivery,
                    'stripe_pkey' => $this->getParameter('stripe_public_key'),
                    'order_reference' => $order->getReference()
                ]
            );
        }

        return $this->redirectToRoute('cart');
    }
}
