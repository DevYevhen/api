<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *  name="users_tbl"
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @JMS\ExclusionPolicy("all")
 */
class User implements UserInterface, \Serializable {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     * @JMS\Expose
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * Uniq index of my sql version can be 767 bytes long.
     * UTF8MB4 uses 4 bytes max for single character, so max varchar
     * field with unique is limited to 767/4 chars.
     * @ORM\Column(type="string", length=191, unique=true)
     * @JMS\Expose
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="Trikoder\Bundle\OAuth2Bundle\Model\Client")
     * @ORM\JoinTable(name="user_clientids_tbl",
     *  joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="identifier", referencedColumnName="identifier", unique=true)}
     * )
     */
    private $clientids;

    public function __construct() {
        $this->isActive = true;
        $this->clientids = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPlainPassword() {
        return $this->plainPassword;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    public function setIsActive($active) {
        $this->isActive = $active;

        return $this;
    }

    public function setPlainPassword($password) {
        $this->plainPassword = $password;

        return $this;
    }

    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    public function getClientids() {
        return $this->clientids->toArray();
    }

    public function addClientid(\Trikoder\Bundle\OAuth2Bundle\Model\Client $token) {
        $this->clientids[] = $token;

        return $this;
    }


    /**
     * System configured to use bcrypt to store passwors which is not require salt
     */
    public function getSalt() {
        return null;
    }

    /**
     * Default role. All other roles will be granted by OAuth2 scope
     */
    public function getRoles() {
        return ['ROLE_USER'];
    }

    public function eraseCredentials() {
    }

    public function unserialize($ser) {
        list(
            $this->id,
            $this->username,
            $this->password
        ) = unserialize($ser, ['allowed_classes' => false]);
    }

    public function serialize() {
        return serialize([
            $this->id, 
            $this->username,
            $this->password
        ]);
    }

}

// vim: sw=4:ts=4:ft=php:expandtab:
