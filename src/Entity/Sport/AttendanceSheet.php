<?php

namespace App\Entity\Sport;

use App\Entity\Tenant\Tenant;
use App\Repository\AttendanceSheetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AttendanceSheetRepository::class)
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class AttendanceSheet
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
     * @ORM\Column(type="string", length=50)
     * @Gedmo\Versioned()
     */
    private $cycle;


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
     * @ORM\ManyToOne(targetEntity=Tenant::class, inversedBy="attendanceSheet")
     */
    private $tenant;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $attendances = [];

    /**
     * @ORM\OneToOne(targetEntity=OrientationSheetPlannings::class, inversedBy="attendanceSheet", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $orientationSheetPlanning;


    public function __construct()
    {
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCycle(): ?string
    {
        return $this->cycle;
    }

    public function setCycle(string $cycle): self
    {
        $this->cycle = $cycle;

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

    public function getAttendances(): ?array
    {
        return $this->attendances;
    }

    public function setAttendances(?array $attendances): self
    {
        $this->attendances = $attendances;

        return $this;
    }

    public function getOrientationSheetPlanning(): ?OrientationSheetPlannings
    {
        return $this->orientationSheetPlanning;
    }

    public function setOrientationSheetPlanning(OrientationSheetPlannings $orientationSheetPlanning): self
    {
        $this->orientationSheetPlanning = $orientationSheetPlanning;

        return $this;
    }

   

   

   
}
