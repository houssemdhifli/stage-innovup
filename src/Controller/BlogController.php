<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Form\AddblogType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        $Posts= $this->getDoctrine()->
        getRepository(BlogPost::class)->findAll();
        $FirstPost=end($Posts);
        return $this->render('blog/blog-home.html.twig', [
            'Posts'=>$Posts,'FirstPost'=>$FirstPost]);
    }
    /**
     * @Route("/Post/{id}", name="Post")
     */
    public function Post($id): Response
    {
        $Post= $this->getDoctrine()->
        getRepository(BlogPost::class)->find($id);
        return $this->render('blog/blog-post.html.twig', [
            'Post'=>$Post]);
    }
    /**
     * @Route("/AddBlogPost",name="AddBlogPost")
     */
    public function AddBlogPost(Request $request ){
        $Post= new BlogPost();
        $form= $this->createForm(AddblogType::class,$Post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $Post->setAuthor($this->getUser());
            $image= $form['img']->getData();
            try {
                if(!is_dir("images_posts")){
                    mkdir("images_posts");
                }
                $filename=$image->getFileName();
                move_uploaded_file($image,"images_posts/".$image->getFileName());
            }
            catch (IOExceptionInterface $e) {
                echo "Erreur Profil existant ou erreur upload image ".$e->getPath();
            }
            $Post->setImg("images_posts/".$image->getFileName());
            $em = $this->getDoctrine()->getManager();
            $em->persist($Post);
            $em->flush();
            return $this->redirectToRoute("blog");
        }
        return $this->render("blog/add.html.twig",array("formAdd"=>$form->createView()));
    }
}
