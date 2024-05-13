<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
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
                                                        Request $request,
                                                        CartService $cartService
    ): Response
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
                $order->setTotal($cartService->getTotal());
                $order->setDeliveryStatus(0);

                $entityManager->persist($order);

                foreach ($cartService->getCart() as $cartItem){
                    $orderItem = new OrderItem();
                    $orderItem->setProduct($cartItem["product"]);
                    $orderItem->setQuantity($cartItem["quantity"]);
                    $orderItem->setOfOrder($order);
                    $entityManager->persist($orderItem);

                }

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
    public function recapOrder($id,OrderRepository $orderRepository): Response
    {
        return $this->render('order/recap.html.twig', [
            'order' => $orderRepository->find($id),
        ]);
    }

    #[Route('/pay/{id}', name: 'app_pay', methods: ['GET'])]
    public function payOrder(Order $order, EntityManagerInterface $entityManager, CartService $cartService):Response
    {
        if($order->getCustomer() ==! $this->getUser()){
            return $this->redirectToRoute("app_product");
        }

        $order->setStatus(1);
        $cartService->emptyCart();

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->render('order/payed.html.twig', ['order'=>$order]);
    }


}



