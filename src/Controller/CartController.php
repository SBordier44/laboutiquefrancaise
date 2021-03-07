<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(protected CartService $cart)
    {
    }

    #[Route('/mon-panier', name: 'cart')]
    public function index(): Response
    {
        return $this->render(
            'cart/index.html.twig',
            [
                'cart' => $this->cart->get()
            ]
        );
    }

    #[Route('/mon-panier/ajout/{productId}', name: 'add_product_to_cart')]
    public function add(
        ProductRepository $productRepository,
        int $productId
    ): Response {
        $product = $productRepository->findOneById($productId);
        if ($product) {
            $this->cart->add($product);
        }
        return $this->redirectToRoute('cart');
    }

    #[Route('/mon-panier/vider-mon-panier', name: 'remove_cart')]
    public function remove(): Response
    {
        $this->cart->remove();
        return $this->redirectToRoute('products');
    }

    #[Route('/mon-panier/delete/{itemId}', name: 'remove_cart_item')]
    public function removeItem(
        int $itemId
    ): Response {
        $this->cart->removeItem($itemId);
        return $this->redirectToRoute('cart');
    }

    #[Route('/mon-panier/decrease/{itemId}', name: 'decrease_cart_item')]
    public function decrease(
        int $itemId
    ): Response {
        $this->cart->decrease($itemId);
        return $this->redirectToRoute('cart');
    }
}
