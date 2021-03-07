<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    #[Route('/mon-compte/mes-adresses', name: 'account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    #[Route('/mon-compte/mes-adresses/nouvelle-adresse', name: 'account_address_create')]
    public function add(
        Request $request
    ): Response {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
            $this->addFlash('success', "L'adresse à bien été ajoutée");
            if ($request->get('referer', false) === 'order') {
                return $this->redirectToRoute('order');
            }
            return $this->redirectToRoute('account_address');
        }

        return $this->render(
            'account/address_add.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/mon-compte/mes-adresses/modifier-une-adresse/{addressId}', name: 'account_address_edit')]
    public function edit(
        Request $request,
        int $addressId
    ): Response {
        $address = $this->getDoctrine()->getRepository(Address::class)->findOneById($addressId);

        if (!$address || $address->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', "L'adresse à bien été modifiée");
            return $this->redirectToRoute('account_address');
        }

        return $this->render(
            'account/address_edit.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/mon-compte/mes-adresses/supprimer-une-adresse/{addressId}', name: 'account_address_remove')]
    public function remove(
        int $addressId
    ): Response {
        $address = $this->getDoctrine()->getRepository(Address::class)->findOneById($addressId);

        if ($address && $address->getUser() === $this->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($address);
            $em->flush();

            $this->addFlash('success', "L'adresse à été supprimée de votre compte avec succès");
        }

        return $this->redirectToRoute('account_address');
    }
}
