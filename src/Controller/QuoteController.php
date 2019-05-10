<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
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

        $quote = $this->getDoctrine()->getRepository(Quote::class)
            ->findBy(['owner' => $user->getId(), 'id' => $id]);

        $view = $this->view($quote, 200);
        return $this->handleView($view);
    }

    /**
     * Create new Quote object
     *
     * @var Request $req Request from user side
     */
    public function postQuoteAction(Request $req) {
        $user = $this->getUser();

        $quote = new Quote();
        $form = $this->createForm(QuoteType::class, $quote);

        $data = json_decode($req->getContent(), true);
        $form->submit($data);

        $quote->setOwner($user);

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
