<?php

namespace App\Entity\Sport;

use App\Entity\Tenant\Tenant;
use App\Repository\OrientationSheetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrientationSheetRepository::class)
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class OrientationSheet
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
     * @ORM\Column(type="date")
     * @Gedmo\Versioned()
     */
    private $sendingDate;

    /**
     * @ORM\Column(type="string", length=300)
     * @Gedmo\Versioned()
     */
    private $situation;

    /**
     * @ORM\Column(type="json")
     * @Gedmo\Versioned()
     */
    private $axes = [];

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $photoAuthorization;


    /**
     * @ORM\ManyToOne(targetEntity=Beneficiary::class, inversedBy="orientationSheets", cascade={"persist"})
     */
    private $beneficiary;

    /**
     * @ORM\ManyToOne(targetEntity=SocialWorker::class, inversedBy="orientationSheets")
     */
    private $socialWorker;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\ManyToOne(targetEntity=Tenant::class, inversedBy="orientationSheet")
     */
    private $tenant;

    /**
     * @ORM\OneToMany(targetEntity=OrientationSheetPlannings::class, mappedBy="orientationSheet", orphanRemoval=true)
     */
    private $plannings;    


    public function __construct()
    {
        $this->plannings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSendingDate(): ?\DateTimeInterface
    {
        return $this->sendingDate;
    }

    public function setSendingDate(\DateTimeInterface $sendingDate): self
    {
        $this->sendingDate = $sendingDate;

        return $this;
    }

    public function getSituation(): ?string
    {
        return $this->situation;
    }

    public function setSituation(string $situation): self
    {
        $this->situation = $situation;

        return $this;
    }

    public function getAxes(): ?array
    {
        return $this->axes;
    }

    public function setAxes(array $axes): self
    {
        $this->axes = $axes;

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

    public function getPhotoAuthorization(): ?bool
    {
        return $this->photoAuthorization;
    }

    public function setPhotoAuthorization(?bool $photoAuthorization): self
    {
        $this->photoAuthorization = $photoAuthorization;

        return $this;
    }

    public function getBeneficiary(): ?Beneficiary
    {
        return $this->beneficiary;
    }

    public function setBeneficiary(?Beneficiary $beneficiary): self
    {
        $this->beneficiary = $beneficiary;

        return $this;
    }

    public function getSocialWorker(): ?SocialWorker
    {
        return $this->socialWorker;
    }

    public function setSocialWorker(?SocialWorker $socialWorker): self
    {
        $this->socialWorker = $socialWorker;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

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

    /**
     * @return Collection|OrientationSheetPlannings[]
     */
    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(OrientationSheetPlannings $planning): self
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings[] = $planning;
            $planning->setOrientationSheet($this);
        }

        return $this;
    }

    public function removePlanning(OrientationSheetPlannings $planning): self
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getOrientationSheet() === $this) {
                $planning->setOrientationSheet(null);
            }
        }

        return $this;
    }

   

    

    
}
