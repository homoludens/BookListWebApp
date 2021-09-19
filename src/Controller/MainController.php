<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\Type\BlogFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\BlogRepository;




class MainController extends AbstractController
{
  /**
   * @Route("/")
   *
   * @param BlogRepository $blogRepository
   *
   * @return Response
   */
  public function index(BlogRepository $blogRepository)
  {
    return $this->render('list.html.twig', ['blogs' => $blogRepository->findAll()]);
  }

  /**
   * @Route("/create")
   *
   * @param Request $request
   *
   * @return Response
   */
  public function createBlog(Request $request,EntityManagerInterface $entityManager)
  {
    $form = $this->createForm(BlogFormType::class, new Blog());

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $blog = $form->getData();
      $entityManager->persist($blog);
      $entityManager->flush();
      $this->addFlash('success', 'Blog was created!');
    }


    return $this->render('create.html.twig', [
      'form' => $form->createView()
    ]);
  }
}