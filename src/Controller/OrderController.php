<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/selection', name: 'app_selection')]
    public function selection(): Response
    {
        $addresses = $this->getUser()->getAddresses();
        $paymentMethods = $this->getUser()->getPaymentMethods();

        $form = $this->createFormBuilder()
            ->add("billing", ChoiceType::class, [
                "choices" => $addresses,
                'choice_label' => "street",
                'choice_value' => "id",
            ])
            ->add("delivery", ChoiceType::class, [
                "choices" => $addresses,
                'choice_label' => "street",
                'choice_value' => "id",
            ])
            ->add("payment", ChoiceType::class, [
                "choices" => $paymentMethods,
                'choice_label' => "cardNumber",
                'choice_value' => "id",
            ])
            ->add('submit', SubmitType::class, [])
            ->setMethod('POST')
            ->setAction($this->generateUrl("app_selection"))
            ->getForm();


        return $this->render('order/selection.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form->createView()
        ]);
    }


    #[Route('/recap')]
    public function recap()
    {

    }


}
