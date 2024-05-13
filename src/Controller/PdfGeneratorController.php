<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Dompdf\Dompdf;

class PdfGeneratorController extends AbstractController
{
    #[Route('/pdf/generator/{id}', name: 'app_pdf_generator')]
    public function index(Order $order): Response
    {
        $data = [
            "customer"=>$order->getCustomer(),
            "products"=>$order->getOrderItems(),
            "deliveryAddress"=>$order->getDeliveryAddress(),
            "billingAddress"=>$order->getBillingAddress(),
            "total"=>$order->getTotal(),
        ];
        $html =  $this->renderView('pdf_generator/index.html.twig', $data);


        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();


        return new Response (
            $dompdf->stream('commande', ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );

    }
}
