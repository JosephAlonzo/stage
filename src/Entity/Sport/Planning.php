<?php

namespace App\Entity\Sport;

use App\Entity\Tenant\Tenant;
use App\Repository\PlanningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanningRepository::class)
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Planning
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    public function __construct()
    {
        $this->startDate = new \DateTime();
        $this->orientationSheets = new ArrayCollection();
    }

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
    private $startDate;

    /**
     * @ORM\Column(type="date")
     * @Gedmo\Versioned()
     */
    private $endDate;

    /**
     * @ORM\Column(type="time")
     * @Gedmo\Versioned()
     */
    private $beginningTime;

    /**
     * @ORM\Column(type="time")
     * @Gedmo\Versioned()
     */
    private $endingTime;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $numberSessions;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $maxPlaces;

    /**
     * @ORM\Column(type="string", length=20)
     * @Gedmo\Versioned()
     */
    private $day;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned()
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Activity::class, inversedBy="plannings")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity=Place::class, inversedBy="plannings")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $place;

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
     * @ORM\ManyToOne(targetEntity=Tenant::class, inversedBy="planning")
     */
    private $tenant;

    /**
     * @ORM\OneToMany(targetEntity=OrientationSheetPlannings::class, mappedBy="planning", orphanRemoval=true)
     */
    private $orientationSheets;

    /**
     * @ORM\ManyToOne(targetEntity=Educator::class, inversedBy="plannings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $educator;

    private $ajaxCall;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getBeginningTime(): ?\DateTimeInterface
    {
        return $this->beginningTime;
    }

    public function setBeginningTime(\DateTimeInterface $beginningTime): self
    {
        $this->beginningTime = $beginningTime;

        return $this;
    }

    public function getEndingTime(): ?\DateTimeInterface
    {
        return $this->endingTime;
    }

    public function setEndingTime(\DateTimeInterface $endingTime): self
    {
        $this->endingTime = $endingTime;

        return $this;
    }

    public function getNumberSessions(): ?int
    {
        return $this->numberSessions;
    }

    public function setNumberSessions(int $numberSessions): self
    {
        $this->numberSessions = $numberSessions;

        return $this;
    }

    public function getMaxPlaces(): ?int
    {
        return $this->maxPlaces;
    }

    public function setMaxPlaces(int $maxPlaces): self
    {
        $this->maxPlaces = $maxPlaces;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getActivity(): ?activity
    {
        return $this->activity;
    }

    public function setActivity(?activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getAjaxCall(): ?bool
    {
        return $this->ajaxCall;
    }

    public function setAjaxCall(?bool $ajaxCall): self
    {
        $this->ajaxCall = $ajaxCall;

        return $this;
    }

    public function getPlace(): ?place
    {
        return $this->place;
    }

    public function setPlace(?place $place): self
    {
        $this->place = $place;

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
    public function getOrientationSheets(): Collection
    {
        return $this->orientationSheets;
    }

    public function addOrientationSheet(OrientationSheetPlannings $orientationSheet): self
    {
        if (!$this->orientationSheets->contains($orientationSheet)) {
            $this->orientationSheets[] = $orientationSheet;
            $orientationSheet->setPlanning($this);
        }

        return $this;
    }

    public function removeOrientationSheet(OrientationSheetPlannings $orientationSheet): self
    {
        if ($this->orientationSheets->removeElement($orientationSheet)) {
            // set the owning side to null (unless already changed)
            if ($orientationSheet->getPlanning() === $this) {
                $orientationSheet->setPlanning(null);
            }
        }

        return $this;
    }

    public function getEducator(): ?Educator
    {
        return $this->educator;
    }

    public function setEducator(?Educator $educator): self
    {
        $this->educator = $educator;

        return $this;
    }
}
