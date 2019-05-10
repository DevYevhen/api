<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends EntityRepository implements UserLoaderInterface {
    public function loadUserByUsername($username) {
        $user = $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();

        if(!$user) {
            return $this->loadUserByClientid($username);
        }

        return $user;
    }

    public function loadUserByClientid($client_id) {
       return $this->createQueryBuilder('u')
            ->join('u.clientids', 'c')
            ->where('c.identifier = :client_id')
            ->setParameter('client_id', $client_id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

// vim: sw=4:ts=4:ft=php:expandtab:
