<?php

namespace App\EventListener;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;

/**
 * Resolve OAuth2 token to user object
 */
final class UserResolverListener {
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;


    /**
     * @param UserProviderInterface $userProvider
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserProviderInterface $userProvider, UserPasswordEncoderInterface $userPasswordEncoder) {
        $this->userProvider = $userProvider;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * Resolve OAuth token to user
     *
     * @param UserResolveEvent $event
     */
    public function onUserResolve(UserResolveEvent $event) {
        $user = $this->userProvider->loadUserByUsername($event->getUsername());

        if (null === $user) {
            return;
        }

        if (!$this->userPasswordEncoder->isPasswordValid($user, $event->getPassword())) {
            return;
        }

        $event->setUser($user);
    }
}

// vim: ts=4:sw=4:ft=php:expandtab:
