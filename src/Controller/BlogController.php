<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Blog;
use App\Form\BlogType;
//use App\Form\BlogFormType;

/**
 * Movie controller.
 * @Route("/api", name="api_")
 */
class BlogController extends AbstractFOSRestController
{
  /**
   * Lists all Movies.
   * @Rest\Get("/bloglist")
   *
   * @return Response
   */
  public function getBlogAction()
  {
    $repository = $this->getDoctrine()->getRepository(Blog::class);
    $blogs = $repository->findall();
    return $this->handleView($this->view($blogs));
  }
  /**
   * Create Blog.
   * @Rest\Post("/blogpost")
   *
   * @return Response
   */
  public function postBlogAction(Request $request)
  {
    $blog = new Blog();
    $form = $this->createForm(BlogType::class, $blog);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($blog);
      $em->flush();
      return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
    }
    return $this->handleView($this->view($form->getErrors()));
  }
}