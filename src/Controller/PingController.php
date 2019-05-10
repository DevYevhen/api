<?php

namespace App\Controller;

use FOS\RestBundle\Controller\FOSRestController;



class PingController extends FOSRestController {

    /**
     * Let's play Ping-pong with user
     */
    public function getPingAction() {
        $number = random_int(0, 100);
        $user = $this->getUser();

        $data = [
            'pong' => $number,
            'user' => ($user)?$user->getUsername():'annon'
        ];

        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}

// vim: sw=4:ts=4:ft=php:expandtab:
