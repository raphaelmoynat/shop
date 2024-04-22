<?php

namespace App\Controller;

use App\Entity\Image;

use App\Entity\Product;
use App\Form\ImageType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ProductRepository $productRepository): Response
    {

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            "products"=>$productRepository->findAll()
        ]);
    }


    #[Route('/admin/create/product', name: 'app_create_product')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {


        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $product->setCreatedAt(new \DateTime());




            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute("app_product");
        }




        return $this->render('product/create.html.twig', [
            "product"=>$product,
            "form"=>$form->createView(),
            "btnValue"=>"Editer"
        ]);
    }

    #[Route('/product/show/{id}', name: 'app_show')]
    public function show(Product $product): Response
    {


        return $this->render('product/show.html.twig', [
            "product"=>$product,
        ]);
    }


    #[Route('/admin/product/delete/{id}', name: 'app_delete_product')]
    public function delete(EntityManagerInterface $manager, Product $product):Response
    {
            $manager->remove($product);
            $manager->flush();
            return $this->redirectToRoute("app_product");
    }

    #[Route('/product/edit/{id}', name: 'app_edit_product')]
    public function edit(Request $request, EntityManagerInterface $manager, Product $product):Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt(new \DateTime());
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute("app_show", ["id" => $product->getId()]);
            }

        return $this->render('product/create.html.twig', [
            "form"=>$form->createView(),
            "btnValue"=>"Editer"
        ]);

    }

    #[Route('/product/images/{id}', name:"product_image")]
    public function addImage(Product $product):Response
    {


            $image = new Image();
            $formImage = $this->createForm(ImageType::class, $image);

            return $this->render("product/image.html.twig", [
                "product" => $product,
                'formImage' => $formImage->createView()

            ]);
    }
}
