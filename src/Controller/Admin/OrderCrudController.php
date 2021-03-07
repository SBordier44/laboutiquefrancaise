<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class OrderCrudController extends AbstractCrudController
{
    public function __construct(protected CrudUrlGenerator $urlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updateToPreparation = Action::new('updateToPreparation', 'Mettre en préparation', 'fas fa-box-open')
            ->linkToCrudAction('updateToPreparation')->setCssClass('mr-2 text-info');

        $updateToDelivery = Action::new('updateToDelivery', 'Mettre en livraison', 'fas fa-truck')
            ->linkToCrudAction('updateToDelivery')->setCssClass('mr-4 text-warning');

        $updateToDelivered = Action::new('updateToDelivered', 'Mettre comme livré', 'fas fa-check')
            ->linkToCrudAction('updateToDelivered')->setCssClass('mr-4 text-success');

        return $actions
            ->add(Crud::PAGE_DETAIL, $updateToPreparation)
            ->add(Crud::PAGE_DETAIL, $updateToDelivery)
            ->add(Crud::PAGE_DETAIL, $updateToDelivered)
            ->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL)
            ->disable(Action::DELETE);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'desc'])
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('reference'),
            AssociationField::new('user', 'Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            DateTimeField::new('createdAt', 'Date')->setTimezone('Europe/Paris'),
            MoneyField::new('total', 'Total produits')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('status', 'Statut')->setChoices(
                [
                    'Non Payée' => 0,
                    'Payée' => 1,
                    'Préparation en cours' => 2,
                    'Livraison en cours' => 3,
                    'Livré' => 4,
                    'Paiement Refusé' => 5
                ]
            ),
            ArrayField::new('orderItems', 'Produits achetés')->hideOnIndex()
        ];
    }

    public function updateToPreparation(AdminContext $context): Response
    {
        $order = $context->getEntity()->getInstance();
        $order->setStatus(Order::STATUS_PREPARE);
        $this->getDoctrine()->getManager()->flush();

        $url = $this->urlGenerator
            ->build()
            ->setController(__CLASS__)
            ->setAction(Action::INDEX)
            ->generateUrl();

        $this->addFlash(
            'notice',
            "<span style='color: green;'>
                        <strong>
                            La commande n°{$order->getReference()} est maintenant au statut \"Préparation en cours\"
                        </strong>
                    </span>"
        );

        return $this->redirect($url);
    }

    public function updateToDelivery(AdminContext $context): Response
    {
        $order = $context->getEntity()->getInstance();
        $order->setStatus(Order::STATUS_SENDED);
        $this->getDoctrine()->getManager()->flush();

        $url = $this->urlGenerator
            ->build()
            ->setController(__CLASS__)
            ->setAction(Action::INDEX)
            ->generateUrl();

        $this->addFlash(
            'notice',
            "<span style='color: royalblue;'>
                        <strong>
                            La commande n°{$order->getReference()} est maintenant au statut \"Livraison en cours\"
                        </strong>
                    </span>"
        );

        return $this->redirect($url);
    }

    public function updateToDelivered(AdminContext $context): Response
    {
        $order = $context->getEntity()->getInstance();
        $order->setStatus(Order::STATUS_DELIVERED);
        $this->getDoctrine()->getManager()->flush();

        $url = $this->urlGenerator
            ->build()
            ->setController(__CLASS__)
            ->setAction(Action::INDEX)
            ->generateUrl();

        $this->addFlash(
            'notice',
            "<span style='color: royalblue;'>
                        <strong>
                            La commande n°{$order->getReference()} est maintenant au statut \"Livré\"
                        </strong>
                    </span>"
        );

        return $this->redirect($url);
    }
}
