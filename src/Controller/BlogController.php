<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Blog;
use App\Form\BlogType;
use Symfony\Component\String\Slugger\SluggerInterface;

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
  public function postBlogAction(Request $request, SluggerInterface $slugger)
  {


//    $metadata = json.loads(request.args['metadata'][0])
//    $file_body = $request.args['file'][0]

    $blog = new Blog();
    $form = $this->createForm(BlogType::class, $blog);

    $metadata_test_1  = $request->getContent();
    $data_1 = json_decode($request->getContent(), true);

    $metadata_test = $request->get('metadata');
    $data = json_decode($metadata_test, true);


    $imageFile = $request->files->get('file');
//    $imageFile = $form->get('imageFile')->getData();
    if ($imageFile) {
      $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
      $safeFilename = $slugger->slug($originalFilename);
      $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

      try {
        $imageFile->move(
          $this->getParameter('image_directory'),
          $newFilename
        );
      } catch (FileException $e) {
        $this->addFlash('error', 'Image cannot be saved.');
      }
      $blog->setImage($newFilename);
    }


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