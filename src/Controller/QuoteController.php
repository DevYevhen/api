<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Author;
use App\Entity\Quote;
use App\Form\QuoteType;



class QuoteController extends FOSRestController {

    /**
     * Fetch random quote for current api user
     */
    public function getQuotesRandomAction() {
        //Fetching random record by ordering by rand() is a little bit nasty
        //Trying to do something smarter a little
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $minmax = $em->createQueryBuilder()
            ->select('MIN(q.id)', 'MAX(q.id)')
            ->from(Quote::class, 'q')
            ->where('q.owner = :owner')
            ->setParameter('owner', $user->getId())
            ->getQuery()
            ->getOneOrNullResult();


        $random = rand($minmax[1], $minmax[2]);

        $rand_entity = $em->createQueryBuilder()
            ->select('q')
            ->from(Quote::class, 'q')
            ->where('q.owner = :owner AND q.id >= :random')
            ->setParameter('owner', $user->getId())
            ->setParameter('random', $random)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        
        $view = $this->view($rand_entity, 200);
        return $this->handleView($view);
        
    }

    /**
     * List quotes owned by registered api user
     */
    public function getQuotesAction() {

        $user = $this->getUser();

        $quotes = $this->getDoctrine()->getRepository(Quote::class)
            ->findBy(['owner' => $user->getId()]);

        $view = $this->view($quotes, 200);
        return $this->handleView($view);
    }

    /**
     * Get Quote object by id
     * @var $id quote id
     */
    public function getQuoteAction($id) {

        $user = $this->getUser();

        $resp = $this->getDoctrine()->getRepository(Quote::class)
            ->findBy(['owner' => $user->getId(), 'id' => $id]);

        $code = Response::HTTP_OK;
        if(empty($resp)) {
            $code = Response::HTTP_NOT_FOUND;
            $resp = ['status' => 'Not found'];
        }


        $view = $this->view($resp, $code);
        return $this->handleView($view);
    }
    /**
     * Delete Quote by id
     * @var $id quote id
     */
    public function deleteQuoteAction($id) {
        $user = $this->getUser();
        $quote = $this->getDoctrine()->getRepository(Quote::class)
            ->findOneBy(['owner' => $user->getId(), 'id' => $id]);

        $code = Response::HTTP_OK;
        $resp = ['status' => 'success'];
        if($quote) {
            $em = $this->getDoctrine()->getManager();
            $author = $quote->getAuthor();
            $author->removeQuote($quote);
            if(count($author->getQuotes()) <= 0) {
                $em->remove($author);
            }
            $em->remove($quote);
            $em->flush();
        }  else {
            $code = Response::HTTP_NOT_FOUND;
            $resp = ['status' => 'not_found'];
        }

        $view = $this->view($resp, $code);
        return $this->handleView($view);
    }

    /**
     * Update Quote by id
     * @var Request $req quote id
     * @var integer $id quote id
     */
    public function putQuoteAction(Request $req, $id) {
        $user = $this->getUser();
        $quote = $this->getDoctrine()->getRepository(Quote::class)
            ->findOneBy(['owner' => $user->getId(), 'id' => $id]);

        $code = Response::HTTP_OK;
        $resp = ['status' => 'success'];
        if($quote) {
            $form = $this->createForm(QuoteType::class, $quote);

            $data = json_decode($req->getContent(), true);
            $form->submit($data);
            if($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($quote);
                $em->flush();
            }
        }  else {
            $code = Response::HTTP_NOT_FOUND;
            $resp = ['status' => 'not_found'];
        }

        $view = $this->view($resp, $code);
        return $this->handleView($view);
    }

    /**
     * Create new Quote object
     *
     * @var Request $req Request from user side
     */
    public function postQuoteAction(Request $req) {
        $user = $this->getUser();
        $data = json_decode($req->getContent(), true);
        $author = null;

        if(isset($data['author']) && isset($data['author']['name'])) {
            $author = $this->getDoctrine()->getRepository(Author::class)
                ->findOneBy(['owner' => $user->getId(), 'name' => $data['author']['name']]);
        }

        $quote = new Quote();
        if($author) {
            $quote->setAuthor($author);
        }
        $form = $this->createForm(QuoteType::class, $quote);


        $form->submit($data);

        //$quote->setOwner($user);

        if($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($quote);
            $em->flush();

            return $this->handleView($this->view(['status' => 'success'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

}

// vim: sw=4:ts=4:ft=php:expandtab:
