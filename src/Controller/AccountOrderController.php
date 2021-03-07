<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    #[Route('/mon-compte/mes-commandes', name: 'account_order')]
    public function index(
        OrderRepository $orderRepository
    ): Response {
        $orders = $orderRepository->findSuccessOrders();
        return $this->render(
            'account/order.html.twig',
            [
                'orders' => $orders
            ]
        );
    }

    #[Route('/mon-compte/mes-commandes/{reference}', name: 'account_order_show')]
    public function show(
        OrderRepository $orderRepository,
        string $reference
    ): Response {
        $order = $orderRepository->findOneByReference($reference);

        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('account_order');
        }
        return $this->render(
            'account/order_show.html.twig',
            [
                'order' => $order
            ]
        );
    }
}
