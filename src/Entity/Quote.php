<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use App\Entity\OwnedEntityInterface;

/**
 * @ORM\Table(
 *  name="quotes_tbl"
 * )
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Quote implements OwnedEntityInterface {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;


    /**
     * @var App\Entity\User $owner Quote owner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var App\Entity\Author $author Quote author
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="quotes", cascade={"persist"})
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * @JMS\Expose
     * @Assert\Valid
     */
    private $author;


    /**
     * @var string $body Quote body
     *
     * @ORM\Column(type="text", nullable=false)
     * @JMS\Expose
     */
    private $body;


    public function getId() {
        return $this->id;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner(\App\Entity\User $owner) {
        $this->owner = $owner;

        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(\App\Entity\Author $author) {
        $this->author = $author;
        return $this;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody(string $body) {
        $this->body = $body;

        return $this;
    }
}

// vim: sw=4:ts=4:ft=php:expandtab:
