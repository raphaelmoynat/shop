<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/manager')]
class ManagerController extends AbstractController
{
    #[Route('/index', name: 'app_manager')]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('manager/index.html.twig', [
            'controller_name' => 'ManagerController',
            "orders"=>$orderRepository->findAll()
        ]);
    }

    #[Route('/show/{id}', name: 'app_manager_show', priority: 2)]
    public function show(Order $order): Response
    {
        return $this->render('manager/show.html.twig', [
            "order"=>$order,
        ]);
    }

    #[Route('/statusdelivery/{id}', name: 'app_manager_statusDelivery', priority: 3)]
    public function changeStatus(Order $order, EntityManagerInterface $manager): Response
    {
        if ($order->getStatus() == 1){
            if ($order->getDeliveryStatus() == 0){
                $order->setDeliveryStatus(1);
            }elseif ($order->getDeliveryStatus() == 1){
                $order->setDeliveryStatus(2);
            }elseif ($order->getDeliveryStatus() == 2){
                $order->setDeliveryStatus(0);
            }
            $manager->persist($order);
            $manager->flush();

        }else{
            return $this->redirectToRoute("app_manager_show", ["id"=>$order->getId()]);


        }

        return $this->redirectToRoute("app_manager_show", ["id"=>$order->getId()]);
    }

}
