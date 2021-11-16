<?php

namespace App\Entity\Sport;

use App\Entity\Tenant\Tenant;
use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Activity
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
     * @ORM\Column(type="string", length=100)
     * @Gedmo\Versioned()
     */
    private $name;


    /**
     * @ORM\OneToMany(targetEntity=Planning::class, mappedBy="activity", orphanRemoval=true)
     */
    private $plannings;

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
     * @ORM\ManyToMany(targetEntity=Educator::class, inversedBy="activities")
     */
    private $educator;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $color;


    /**
     * @ORM\ManyToOne(targetEntity=Tenant::class, inversedBy="activity")
     */
    private $tenant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxPlaces;

    public function __construct()
    {
        $this->plannings = new ArrayCollection();
        $this->educator = new ArrayCollection();
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


    /**
     * @return Collection|Planning[]
     */
    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings[] = $planning;
            $planning->setActivity($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getActivity() === $this) {
                $planning->setActivity(null);
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
     * @return Collection|Educator[]
     */
    public function getEducator(): Collection
    {
        return $this->educator;
    }

    public function addEducator(Educator $educator): self
    {
        if (!$this->educator->contains($educator)) {
            $this->educator[] = $educator;
        }

        return $this;
    }

    public function removeEducator(Educator $educator): self
    {
        $this->educator->removeElement($educator);

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

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

    public function getMaxPlaces(): ?int
    {
        return $this->maxPlaces;
    }

    public function setMaxPlaces(?int $maxPlaces): self
    {
        $this->maxPlaces = $maxPlaces;

        return $this;
    }
}
