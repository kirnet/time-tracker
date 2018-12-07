<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string The user login
     * @ORM\Column(type="string", unique=true)
     */
    private $login;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBanned = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registerAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Timer", mappedBy="user", orphanRemoval=true)
     */
    private $timer;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="users")
     */
    private $projects;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $apiToken;

    /**
     * User constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->registerAt = new \DateTime();
        $this->projects = new ArrayCollection();
        $this->timer = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getIsBanned()
    {
        return $this->isBanned;
    }

    /**
     * @param mixed $isBanned
     */
    public function setIsBanned($isBanned): void
    {
        $this->isBanned = $isBanned;
    }

    /**
     * @return mixed
     */
    public function getRegisterAt()
    {
        return $this->registerAt;
    }

    /**
     * @param mixed $registerAt
     */
    public function setRegisterAt($registerAt): void
    {
        $this->registerAt = $registerAt;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login): void
    {
        $this->login = $login;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        if (empty($roles)) {
            $this->roles[] = 'ROLE_USER';
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([$this->id, $this->email, $this->password]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->email, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return Collection|Timer[]
     */
    public function getTimer(): Collection
    {
        return $this->timer;
    }

    /**
     * @param Timer $timer
     *
     * @return User
     */
    public function addTimer(Timer $timer): self
    {
        if (!$this->timer->contains($timer)) {
            $this->timer[] = $timer;
            $timer->setUser($this);
        }

        return $this;
    }

    /**
     * @param Timer $timer
     *
     * @return User
     */
    public function removeTimer(Timer $timer): self
    {
        if ($this->timer->contains($timer)) {
            $this->timer->removeElement($timer);
            if ($timer->getUser() === $this) {
                $timer->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    /**
     * @param Project $project
     *
     * @return User
     */
    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addUser($this);
        }

        return $this;
    }

    /**
     * @param Project $project
     *
     * @return User
     */
    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeUser($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }
}
