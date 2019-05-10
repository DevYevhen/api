<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\OwnedEntityInterface;


class EntityOwnershipListener {

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Set ownership for Author and Qoute objects before persis
     *
     * @var LifecycleEventArgs $event
     * @return void
     */
    public function prePersist(LifecycleEventArgs $event) {
        $entity = $event->getEntity();

        if(!($entity instanceof OwnedEntityInterface)) {
            return;
        }

        if($entity->getOwner()) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if(!$token) {
            return;
        }

        $entity->setOwner($token->getUser());
    }
}

// vim: sw=4:ts=4:ft=php:expandtab:
