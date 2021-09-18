<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;




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
    return $this->render('list.html.twig');
  }
}