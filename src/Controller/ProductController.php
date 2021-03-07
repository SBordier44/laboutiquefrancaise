<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\SearchType;
use App\Dto\Search;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public function __construct(protected ProductRepository $productRepository)
    {
    }

    #[Route('/nos-produits', name: 'products')]
    public function index(
        Request $request
    ): Response {
        $search = new Search();
        $searchForm = $this->createForm(SearchType::class, $search);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $products = $this->productRepository->findWithSearch($search);
        } else {
            $products = $this->productRepository->findAll();
        }

        return $this->render(
            'product/index.html.twig',
            [
                'search_form' => $searchForm->createView(),
                'products' => $products
            ]
        );
    }

    #[Route('/produits/{slug}', name: 'product_show')]
    public function show(
        string $slug
    ): Response {
        $product = $this->productRepository->findOneBySlug($slug);
        $bestProducts = $this->productRepository->findByIsBest(true);

        if (!$product) {
            return $this->redirectToRoute('products');
        }

        return $this->render(
            'product/show.html.twig',
            [
                'product' => $product,
                'bestProducts' => $bestProducts
            ]
        );
    }
}
