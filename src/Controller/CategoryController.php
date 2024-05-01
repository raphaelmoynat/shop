<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute("app_category");
        }

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories'=> $categoryRepository->findAll(),
            'form'=> $form->createView()
        ]);
    }

    #[Route('admin/category/create', name: 'app_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute("app_category");
        }


        return $this->render('category/create.html.twig', [

            'form'=> $form->createView()
        ]);
    }
}
