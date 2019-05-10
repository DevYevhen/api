<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController{

    /**
     * Render login page
     * @var AuthenticationUtils $au
     *
     * @Route("/user/login", name="login")
     */
    public function loginAction(AuthenticationUtils $au) {
        $err = $au->getLastAuthenticationError();

        $lastUsername = $au->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $err,
        ]);
    }
}


// vim: sw=4:ts=4:ft=php:expandtab:
