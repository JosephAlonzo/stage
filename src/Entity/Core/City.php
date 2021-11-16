<?php

namespace App\Entity\Core;

use App\Entity\Sport\Beneficiary;
use App\Entity\Sport\SocialWorker;
use App\Entity\Sport\Place;
use App\Entity\Sport\Structure;
use App\Entity\Tenant\Tenant;
use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class City
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
    private $name;

    /**
     * @ORM\Column(type="string", length=5)
     * @Gedmo\Versioned()
     */
    private $postalCode;

    /**
     * @ORM\OneToMany(targetEntity=App\Entity\Sport\Beneficiary::class, mappedBy="city", orphanRemoval=true)
     */
    private $beneficiaries;

    /**
     * @ORM\OneToMany(targetEntity=App\Entity\Sport\SocialWorker::class, mappedBy="city", orphanRemoval=true)
     */
    private $socialWorkers;

    /**
     * @ORM\OneToMany(targetEntity=App\Entity\Sport\Place::class, mappedBy="city")
     */
    private $places;

    /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @Gedmo\Versioned()
     */
    private $createdBy;

    /**
     * @var User $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @Gedmo\Versioned()
     */
    private $updatedBy;

    /**
     * @ORM\OneToMany(targetEntity=Structure::class, mappedBy="city")
     */
    private $structures;

    /**
     * @ORM\OneToMany(targetEntity=Tenant::class, mappedBy="city")
     */
    private $tenants;

    public function __construct()
    {
        $this->beneficiaries = new ArrayCollection();
        $this->socialWorkers = new ArrayCollection();
        $this->places = new ArrayCollection();
        $this->structures = new ArrayCollection();
        $this->tenants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return Collection|Beneficiary[]
     */
    public function getBeneficiaries(): Collection
    {
        return $this->beneficiaries;
    }

    public function addBeneficiary(Beneficiary $beneficiary): self
    {
        if (!$this->beneficiaries->contains($beneficiary)) {
            $this->beneficiaries[] = $beneficiary;
            $beneficiary->setCity($this);
        }

        return $this;
    }

    public function removeBeneficiary(Beneficiary $beneficiary): self
    {
        if ($this->beneficiaries->removeElement($beneficiary)) {
            // set the owning side to null (unless already changed)
            if ($beneficiary->getCity() === $this) {
                $beneficiary->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SocialWorker[]
     */
    public function getSocialWorkers(): Collection
    {
        return $this->socialWorkers;
    }

    public function addSocialWorker(SocialWorker $socialWorker): self
    {
        if (!$this->socialWorkers->contains($socialWorker)) {
            $this->socialWorkers[] = $socialWorker;
            $socialWorker->setCity($this);
        }

        return $this;
    }

    public function removeSocialWorker(SocialWorker $socialWorker): self
    {
        if ($this->socialWorkers->removeElement($socialWorker)) {
            // set the owning side to null (unless already changed)
            if ($socialWorker->getCity() === $this) {
                $socialWorker->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Place[]
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->setCity($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getCity() === $this) {
                $place->setCity(null);
            }
        }

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
     * @return Collection|Structure[]
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }

    public function addStructure(Structure $structure): self
    {
        if (!$this->structures->contains($structure)) {
            $this->structures[] = $structure;
            $structure->setCity($this);
        }

        return $this;
    }

    public function removeStructure(Structure $structure): self
    {
        if ($this->structures->removeElement($structure)) {
            // set the owning side to null (unless already changed)
            if ($structure->getCity() === $this) {
                $structure->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tenant[]
     */
    public function getTenants(): Collection
    {
        return $this->tenants;
    }

    public function addTenant(Tenant $tenant): self
    {
        if (!$this->tenants->contains($tenant)) {
            $this->tenants[] = $tenant;
            $tenant->setCity($this);
        }

        return $this;
    }

    public function removeTenant(Tenant $tenant): self
    {
        if ($this->tenants->removeElement($tenant)) {
            // set the owning side to null (unless already changed)
            if ($tenant->getCity() === $this) {
                $tenant->setCity(null);
            }
        }

        return $this;
    }

    public function hasRelations() {
        if (
            !$this->getBeneficiaries()->isEmpty()
        ||  !$this->getSocialWorkers()->isEmpty()
        ||  !$this->getPlaces()->isEmpty()
        ||  !$this->getStructures()->isEmpty()
        ||  !$this->getTenants()->isEmpty()
        ){
            return true;
        }
    } 
}
