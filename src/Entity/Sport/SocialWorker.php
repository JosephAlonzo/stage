<?php

namespace App\Entity\Sport;

use App\Entity\Core\City;
use App\Entity\Security\User;
use App\Entity\Tenant\Tenant;

use App\Repository\SocialWorkerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SocialWorkerRepository::class)
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class SocialWorker
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
     */
    private $origin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity=App\Entity\Core\City::class, inversedBy="socialWorkers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $city;


    /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var User $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @ORM\OneToMany(targetEntity=OrientationSheet::class, mappedBy="socialWorker")
     */
    private $orientationSheets;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="socialWorker", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Structure::class, inversedBy="socialWorkers")
     */
    private $structure;

    /**
     * @ORM\ManyToOne(targetEntity=Tenant::class, inversedBy="SocialWorker")
     */
    private $tenant;

    public function __construct()
    {
        $this->orientationSheets = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }


    public function getCity(): ?city
    {
        return $this->city;
    }

    public function setCity(?city $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @return Collection|OrientationSheet[]
     */
    public function getOrientationSheets(): Collection
    {
        return $this->orientationSheets;
    }

    public function addOrientationSheet(OrientationSheet $orientationSheet): self
    {
        if (!$this->orientationSheets->contains($orientationSheet)) {
            $this->orientationSheets[] = $orientationSheet;
            $orientationSheet->setSocialWorker($this);
        }

        return $this;
    }

    public function removeOrientationSheet(OrientationSheet $orientationSheet): self
    {
        if ($this->orientationSheets->removeElement($orientationSheet)) {
            // set the owning side to null (unless already changed)
            if ($orientationSheet->getSocialWorker() === $this) {
                $orientationSheet->setSocialWorker(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function setStructure(?Structure $structure): self
    {
        $this->structure = $structure;

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


}
