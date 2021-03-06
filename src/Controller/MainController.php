<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\BlogRepository;
use Symfony\Component\String\Slugger\SluggerInterface;


class MainController extends AbstractController
{
  /**
   * @Route("/", name="app_index")
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
  public function createBlog(Request $request,EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
  {
    $form = $this->createForm(BlogFormType::class, new Blog());

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $blog = $form->getData();
      $imageFile = $form->get('imageFile')->getData();
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

      $entityManager->persist($blog);
      $entityManager->flush();

      $this->addFlash('success', 'Blog was created!');

      return $this->redirectToRoute('app_index');
    }

    return $this->render(
      'create.html.twig', [
        'form' => $form->createView(),
      ]
    );
  }

  /**
   * @Route("/edit/{id}")
   *
   * @ParamConverter("blog", class="App:Blog")
   *
   * @return Response
   */
  public function editBlog(Blog $blog, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
  {
    if ($blog->getImage()) {
      $blog->setImage(new File(sprintf('%s/%s', $this->getParameter('image_directory'), $blog->getImage())));
    }
    $form = $this->createForm(BlogFormType::class, $blog);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $blog      = $form->getData();
      $imageFile = $form->get('imageFile')->getData();
      if ($imageFile) {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $slugger->slug($originalFilename);
        $newFilename  = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

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

      $entityManager->persist($blog);
      $entityManager->flush();
      $this->addFlash('success', 'Blog was edited!');
    }

    return $this->render('create.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  /**
   * @Route("/delete/{id}", name="app_blog_delete")
   *
   * @param Blog                   $blog
   * @param EntityManagerInterface $em
   *
   * @return RedirectResponse
   */
  public function deleteBlog(Blog $blog, EntityManagerInterface $em): RedirectResponse
  {
    $em->remove($blog);
    $em->flush();
    $this->addFlash('success', 'Blog was deleted!');

    return $this->redirectToRoute('app_index');
  }

  /**
   * @Route("/deleteallbooks")
   */
  public function deleteAllBooks(EntityManagerInterface $em)
  {
    $repository = $em->getRepository(Blog::class);
    $entities = $repository->findAll();

    foreach ($entities as $entity) {
      $em->remove($entity);
    }
    $em->flush();

    $this->addFlash('success', 'All Books were deleted!');
    return $this->redirectToRoute('app_index');

  }


  /**
   * @Route("/view/{id}", name="app_blog_view")
   *
   * @param Blog                   $blog
   * @param EntityManagerInterface $em
   *
   * @return RedirectResponse
   */
  public function viewBlog(Blog $blog, EntityManagerInterface $em, BlogRepository $blogRepository)
  {

    $this->addFlash('success', 'Blog was viewed!');
//    $blogRepository->find()
    return $this->render('viewblog.html.twig', ['blog' => $blog]);
//    $em->remove($blog);
//    $em->flush();


  }

}