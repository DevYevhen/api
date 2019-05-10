<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Author;
use App\Form\AuthorType;



class AuthorController extends FOSRestController {

    /**
     * List authors owned by registered api user
     */
    public function getAuthorsAction() {

        $user = $this->getUser();

        $authors = $this->getDoctrine()->getRepository(Author::class)
            ->findBy(['owner' => $user->getId()]);

        $view = $this->view($authors, 200);
        return $this->handleView($view);
    }

    /**
     * Get Author object by id
     * @var $id author id
     */
    public function getAuthorAction($id) {

        $user = $this->getUser();

        $author = $this->getDoctrine()->getRepository(Author::class)
            ->findBy(['owner' => $user->getId(), 'id' => $id]);

        $view = $this->view($author, 200);
        return $this->handleView($view);
    }

    /**
     * Create new Author object
     *
     * @var Request $req Request from user side
     */

    public function postAuthorAction(Request $req) {

        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $data = json_decode($req->getContent(), true);
        $form->submit($data);


        if($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            return $this->handleView($this->view(['status' => 'success'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }
}

// vim: sw=4:ts=4:ft=php:expandtab:
