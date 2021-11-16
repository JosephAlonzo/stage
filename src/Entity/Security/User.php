<?php

namespace App\Entity\Security;

use App\Entity\Sport\Educator;
use App\Entity\Sport\SocialWorker;
use App\Entity\Tenant\Tenant;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Security\Core\User\UserInterface;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Gedmo\Loggable
 */
class User implements UserInterface
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     * @Gedmo\Versioned()
     */
    private $roles = [];

        /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isEnabled;

    /**
     * @ORM\OneToOne(targetEntity=SocialWorker::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $socialWorker;

    /**
     * @ORM\ManyToOne(targetEntity=Tenant::class, inversedBy="User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tenant;

    /**
     * @ORM\OneToOne(targetEntity=Educator::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $educator;


    public function __construct()
    {
        $this->isEnabled = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        
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
    
    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSocialWorker(): ?SocialWorker
    {
        return $this->socialWorker;
    }

    public function setSocialWorker(?SocialWorker $socialWorker): self
    {
        // unset the owning side of the relation if necessary
        if ($socialWorker === null && $this->socialWorker !== null) {
            $this->socialWorker->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($socialWorker !== null && $socialWorker->getUser() !== $this) {
            $socialWorker->setUser($this);
        }

        $this->socialWorker = $socialWorker;

        return $this;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getEducator(): ?Educator
    {
        return $this->educator;
    }

    public function setEducator(?Educator $educator): self
    {
        // unset the owning side of the relation if necessary
        if ($educator === null && $this->educator !== null) {
            $this->educator->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($educator !== null && $educator->getUser() !== $this) {
            $educator->setUser($this);
        }

        $this->educator = $educator;

        return $this;
    }
}
