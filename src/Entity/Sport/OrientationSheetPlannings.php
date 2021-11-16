<?php

namespace App\Entity\Sport;

use App\Repository\Sport\OrientationSheetPlanningsRepository;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=OrientationSheetPlanningsRepository::class)
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class OrientationSheetPlannings
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
     * @ORM\Column(type="boolean")
     */
    private $confirmed = false;

    /**
     * @ORM\ManyToOne(targetEntity=OrientationSheet::class, inversedBy="plannings",cascade={"persist"}))
     * @ORM\JoinColumn(nullable=false)
     */
    private $orientationSheet;

    /**
     * @ORM\ManyToOne(targetEntity=Planning::class, inversedBy="orientationSheets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planning;

    /**
     * @ORM\OneToOne(targetEntity=AttendanceSheet::class, mappedBy="orientationSheetPlanning", cascade={"persist", "remove"})
     */
    private $attendanceSheet;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getOrientationSheet(): ?OrientationSheet
    {
        return $this->orientationSheet;
    }

    public function setOrientationSheet(?OrientationSheet $orientationSheet): self
    {
        $this->orientationSheet = $orientationSheet;

        return $this;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): self
    {
        $this->planning = $planning;

        return $this;
    }

    public function getAttendanceSheet(): ?AttendanceSheet
    {
        return $this->attendanceSheet;
    }

    public function setAttendanceSheet(AttendanceSheet $attendanceSheet): self
    {
        // set the owning side of the relation if necessary
        if ($attendanceSheet->getOrientationSheetPlanning() !== $this) {
            $attendanceSheet->setOrientationSheetPlanning($this);
        }

        $this->attendanceSheet = $attendanceSheet;

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

   
}
