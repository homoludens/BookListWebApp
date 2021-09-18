<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;




class MainController
{
  /**
   * @Route("/", name="index")
   */
  public function index()
  {
    return new Response('test');
  }
}