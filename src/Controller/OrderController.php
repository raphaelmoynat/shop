<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentMethodRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/order')]
class OrderController extends AbstractController
{

    #[Route('/selection', name: 'app_selection', methods: ['GET', 'POST'], priority: 5)]
    public function selection(                          AddressRepository $addressRepository,
                                                        PaymentMethodRepository $paymentMethodRepository,
                                                        EntityManagerInterface $entityManager,
                                                        Request $request): Response
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
            ->setMethod('POST')
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            if ($formData['billing']=== null || $formData['delivery']===null || $formData['payment']===null )
            {
                return $this->render('order/selection.html.twig', [
                    'controller_name' => 'OrderController',
                    'form' => $form->createView()
                ]);

            }else{

                $order = new Order();
                $order->setCustomer($this->getUser());
                $order->setBillingAddress($formData['billing']);
                $order->setDeliveryAddress($formData['delivery']);
                $order->setPaymentMethod($formData['payment']);
                $order->setStatus(0);
                $order->setDeliveryStatus(0);

                $entityManager->persist($order);
                $entityManager->flush();
                return $this->redirectToRoute('app_recap_order', ['id' => $order->getId()]);

            }

        }

        return $this->render('order/selection.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form->createView()
        ]);
    }



    #[Route('/recap/{id}', name: 'app_recap_order', methods: ['POST', 'GET'])]
    public function recapOrder($id,OrderRepository $orderRepository, CartService $cartService): Response
    {
        return $this->render('order/recap.html.twig', [
            'order' => $orderRepository->find($id),
            'total' =>$cartService->getTotal(),
            'items' =>$cartService->getCart()
        ]);
    }
}



