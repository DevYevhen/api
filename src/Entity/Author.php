<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use App\Entity\OwnedEntityInterface;

/**
 * @ORM\Table(
 *  name="authors_tbl"
 * )
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Author implements OwnedEntityInterface {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;



    /**
     * @var App\Entity\User $owner Author owner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $owner;


    /**
     * @var ArrayCollection $quotes Author's quotes
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Quote", mappedBy="author")
     */
    private $quotes;

    /**
     *
     * @var string $name Author's name
     *
     * @ORM\Column(name="name", type="string", length=191, nullable=false, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *  min=1,
     *  max=191,
     *  minMessage="Auhtor's name must be at least {{ limit }} chars long",
     *  maxMessage="Author's name cannot be longer than {{ limit }} chars"
     * )
     * @JMS\Expose
     */
    public $name;


    public function __construct() {
        $this->quotes = new ArrayCollection();
    }


    public function getId() {
        return $this->id;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner(\App\Entity\User $owner) {
        $this->owner = $owner;
    }

    public function getQuotes()  {
        return $this->quotes->toArray();
    }

    public function addQuote(\App\Entity\Quote $quote) {
        $this->quotes[] = $quote;

        $quote->setAuthor($this);

        return $this;
    }

    public function removeQuote(\App\Entity\Quote $quote) {
        $this->quotes->removeElement($quote);
        $quote->setAuthor(NULL);

        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;

        return $this;
    }
}

// vim: sw=4:ts=4:ft=php:expandtab:
