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
        return $this->render('blog/blog-home.html.twig', [
            'controller_name' => 'BlogController',
        ]);
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

                // rename("images_products/".$image->getFileName() , "images_products/".$NGO->getNGO().".".$image->getClientOriginalExtension());
                rename("images_posts/".$image->getFileName() , "images_posts/".$Post->getTitle().".".$image->getClientOriginalExtension());
            }
            catch (IOExceptionInterface $e) {
                echo "Erreur Profil existant ou erreur upload image ".$e->getPath();
            }
            $Post->setImg("images_NGO/".$Post->getTitle().".".$image->getClientOriginalExtension ());
            $em = $this->getDoctrine()->getManager();
            $em->persist($Post);
            $em->flush();
            return $this->redirectToRoute("blog");
        }
        return $this->render("blog/add.html.twig",array("formAdd"=>$form->createView()));
    }
}
