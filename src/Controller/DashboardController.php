<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractController{

    /**
     * User dashboard page (generate client_id and client_secret)
     *
     * @var Request $req
     * @Route("/user/dashboard", name="user_dashboard")
     */
    public function dashboardAction(Request $req) {

        $user = $this->getUser();

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
        ]);
    }
}


// vim: sw=4:ts=4:ft=php:expandtab:
