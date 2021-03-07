<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    public function __construct(protected SessionInterface $session, protected ProductRepository $productRepository)
    {
    }

    public function add(Product $product): ?array
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$product->getId()])) {
            $cart[$product->getId()]++;
        } else {
            $cart[$product->getId()] = 1;
        }

        return $this->session->set('cart', $cart);
    }

    public function get(): array
    {
        $cartComplete = [];

        foreach ($this->session->get('cart', []) as $productId => $quantity) {
            $product = $this->productRepository->findOneById($productId);

            if (!$product) {
                $this->removeItem($productId);
                continue;
            }

            $cartComplete[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }

        return $cartComplete;
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function removeItem(int $itemId)
    {
        $cart = $this->session->get('cart', []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
        }

        return $this->session->set('cart', $cart);
    }

    public function decrease(int $itemId)
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$itemId])) {
            if ($cart[$itemId] > 1) {
                $cart[$itemId]--;
            } else {
                unset($cart[$itemId]);
            }
            return $this->session->set('cart', $cart);
        }
        return $cart;
    }
}
