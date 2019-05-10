<?php

namespace App\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Trikoder\Bundle\OAuth2Bundle\Manager\ClientManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;
use Trikoder\Bundle\OAuth2Bundle\Model\RedirectUri;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;


class CredentialsController extends FOSRestController {

    private $ci;

    public function __construct(ClientManagerInterface $ci) {
        $this->ci = $ci;
    }

    /**
     * Generate credentials for user
     */
    public function postUsercredsAction() {
        $user = $this->getUser();
        $creds = $user->getClientids();


        if(count($creds) <= 0 ) {
            $identifier = hash('md5', random_bytes(16));
            $secret = hash('sha512', random_bytes(32));
            $client = new Client($identifier, $secret);
            $client->setActive(true);

            $this->ci->save($client);
            $user->addClientid($client);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

        } else {
            //get first credential from user entity
            $client = array_shift($creds);
        }

                    

        $view = $this->view([
            'status' => 'success',
            'client_id' => $client->getIdentifier(),
            'client_secret' => $client->getSecret()
        ] , 200);
        return $this->handleView($view);
    }
}

// vim: sw=4:ts=4:ft=php:expandtab:
