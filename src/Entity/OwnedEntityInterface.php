<?php

namespace App\Entity;
use App\Entity\User;


interface OwnedEntityInterface {
    public function getOwner();
    public function setOwner(User $owner);
}

// vim: sw=4:ts=4:ft=php:expandtab:
