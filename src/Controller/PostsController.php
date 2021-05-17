<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostsController extends AbstractController
{
    /**
     * @Route("/registrar-posts", name="RegistarPosts")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $form = $this->createForm(PostsType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('foto')->getData();

            if($brochureFile){
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                //Move to directory
                try{
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    throw new \Exception('Ha ocurrido un error!');
                }

                $post->setFoto($newFilename);
            }

            $user = $this->getUser();
            $post->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('posts/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/{id}", name="VerPost")
     */

    public function verPost($id){
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        return $this->render('posts/verPost.html.twig',[
            'post' => $post
        ]);
    }

    /**
     * @Route("/mis-posts", name="MisPosts")
     */
    public function misPost(){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $posts = $em->getRepository(Post::class)->findBy(['user'=>$user]);

        return $this->render('posts/misPosts.html.twig',[
            'posts' => $posts
        ]);
    }
}
