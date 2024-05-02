<?php

namespace App\Controller;

use App\Entity\Comment;

use App\Entity\Product;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route('/comment/create/{id}', name: 'app_comment_create')]
    public function create(Request $request, EntityManagerInterface $manager, Product $product):Response
    {
        if(!$this->getUser()){return $this->redirectToRoute("app_product");}

        $comment= new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $comment->setCreatedAt(new \DateTime());
            $comment->setProduct($product);
            $comment->setAuthor($this->getUser());
            $manager->persist($comment);
            $manager->flush();

        }

        return $this->redirectToRoute("app_show",["id"=>$product->getId()]);


    }


    #[Route('/comment/delete/{id}', name: 'delete_comment')]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {

        if($this->getUser() === $comment->getAuthor() || $this->isGranted('ROLE_ADMIN')) {
            $product = $comment->getProduct();

            $manager->remove($comment);
            $manager->flush();

            return $this->redirectToRoute('app_show', ["id" => $product->getId()]);
        }else{
            return $this->redirectToRoute('app_product');
        }
    }


    #[Route('/comment/edit/{id}', name: 'edit_comment')]
    public function edit(Request $request, EntityManagerInterface $manager, Comment $comment):Response
    {
        $product = $comment->getProduct();
        $form = $this->createForm(CommentType::class, $comment);


        if($this->getUser() === $comment->getAuthor() || $this->isGranted('ROLE_ADMIN')) {

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setCreatedAt(new \DateTime());

                $manager->persist($comment);
                $manager->flush();

                return $this->redirectToRoute("app_show", ["id" => $product->getId()]);
            }
        }else{
            return $this->redirectToRoute('app_product');
        }

        return $this->render('comment/edit.html.twig', [
            "form"=>$form->createView(),
        ]);

    }
}
