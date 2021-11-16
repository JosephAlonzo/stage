<?php

namespace App\Entity\Tenant;

use App\Entity\Core\Holiday;
use App\Entity\Security\User;
use App\Entity\Sport\Activity;
use App\Entity\Sport\AttendanceSheet;
use App\Entity\Sport\Beneficiary;
use App\Entity\Sport\Educator;
use App\Entity\Sport\Place;
use App\Entity\Sport\Planning;
use App\Entity\Sport\SocialWorker;
use App\Entity\Sport\Structure;
use App\Entity\Sport\orientationSheet;
use App\Repository\Tenant\TenantRepository;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Core\City;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=tenantRepository::class)
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Tenant
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siteInternet;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $codeApe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cdosName;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $cdosNumber;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="tenant")
     */
    private $User;

    /**
     * @ORM\OneToMany(targetEntity=SocialWorker::class, mappedBy="tenant")
     */
    private $SocialWorker;

    /**
     * @ORM\OneToMany(targetEntity=Educator::class, mappedBy="tenant")
     */
    private $educator;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="tenant")
     */
    private $activity;

    /**
     * @ORM\OneToMany(targetEntity=Planning::class, mappedBy="tenant")
     */
    private $planning;

    /**
     * @ORM\OneToMany(targetEntity=Place::class, mappedBy="tenant")
     */
    private $place;

    /**
     * @ORM\OneToMany(targetEntity=Structure::class, mappedBy="tenant")
     */
    private $structure;

    /**
     * @ORM\OneToMany(targetEntity=Beneficiary::class, mappedBy="tenant")
     */
    private $beneficiary;

    /**
     * @ORM\OneToMany(targetEntity=AttendanceSheet::class, mappedBy="tenant")
     */
    private $attendanceSheet;

    /**
     * @ORM\OneToMany(targetEntity=orientationSheet::class, mappedBy="tenant")
     */
    private $orientationSheet;

    

    /**
     * @ORM\OneToMany(targetEntity=Holiday::class, mappedBy="tenant")
     */
    private $holiday;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="tenants")
     */
    private $city;

    public function __construct()
    {
        $this->User = new ArrayCollection();
        $this->SocialWorker = new ArrayCollection();
        $this->educator = new ArrayCollection();
        $this->activity = new ArrayCollection();
        $this->planning = new ArrayCollection();
        $this->place = new ArrayCollection();
        $this->structure = new ArrayCollection();
        $this->beneficiary = new ArrayCollection();
        $this->attendanceSheet = new ArrayCollection();
        $this->orientationSheet = new ArrayCollection();
        $this->holiday = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteInternet(): ?string
    {
        return $this->siteInternet;
    }

    public function setSiteInternet(?string $siteInternet): self
    {
        $this->siteInternet = $siteInternet;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getCodeApe(): ?string
    {
        return $this->codeApe;
    }

    public function setCodeApe(?string $codeApe): self
    {
        $this->codeApe = $codeApe;

        return $this;
    }

    public function getCdosName(): ?string
    {
        return $this->cdosName;
    }

    public function setCdosName(string $cdosName): self
    {
        $this->cdosName = $cdosName;

        return $this;
    }

    public function getCdosNumber(): ?string
    {
        return $this->cdosNumber;
    }

    public function setCdosNumber(string $cdosNumber): self
    {
        $this->cdosNumber = $cdosNumber;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->User;
    }

    public function addUser(User $user): self
    {
        if (!$this->User->contains($user)) {
            $this->User[] = $user;
            $user->setTenant($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->User->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTenant() === $this) {
                $user->setTenant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SocialWorker[]
     */
    public function getSocialWorker(): Collection
    {
        return $this->SocialWorker;
    }

    public function addSocialWorker(SocialWorker $socialWorker): self
    {
        if (!$this->SocialWorker->contains($socialWorker)) {
            $this->SocialWorker[] = $socialWorker;
            $socialWorker->setTenant($this);
        }

        return $this;
    }

    public function removeSocialWorker(SocialWorker $socialWorker): self
    {
        if ($this->SocialWorker->removeElement($socialWorker)) {
            // set the owning side to null (unless already changed)
            if ($socialWorker->getTenant() === $this) {
                $socialWorker->setTenant(null);
            }
        }

        return $this;
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
            $educator->setTenant($this);
        }

        return $this;
    }

    public function removeEducator(Educator $educator): self
    {
        if ($this->educator->removeElement($educator)) {
            // set the owning side to null (unless already changed)
            if ($educator->getTenant() === $this) {
                $educator->setTenant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivity(): Collection
    {
        return $this->activity;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activity->contains($activity)) {
            $this->activity[] = $activity;
            $activity->setTenant($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activity->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getTenant() === $this) {
                $activity->setTenant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Planning[]
     */
    public function getPlanning(): Collection
    {
        return $this->planning;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->planning->contains($planning)) {
            $this->planning[] = $planning;
            $planning->setTenant($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->planning->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getTenant() === $this) {
                $planning->setTenant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Place[]
     */
    public function getPlace(): Collection
    {
        return $this->place;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->place->contains($place)) {
            $this->place[] = $place;
            $place->setTenant($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->place->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getTenant() === $this) {
                $place->setTenant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Structure[]
     */
    public function getStructure(): Collection
    {
        return $this->structure;
    }

    public function addStructure(Structure $structure): self
    {
        if (!$this->structure->contains($structure)) {
            $this->structure[] = $structure;
            $structure->setTenant($this);
        }

        return $this;
    }

    public function removeStructure(Structure $structure): self
    {
        if ($this->structure->removeElement($structure)) {
            // set the owning side to null (unless already changed)
            if ($structure->getTenant() === $this) {
                $structure->setTenant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Beneficiary[]
     */
    public function getBeneficiary(): Collection
    {
        return $this->beneficiary;
    }

    public function addBeneficiary(Beneficiary $beneficiary): self
    {
        if (!$this->beneficiary->contains($beneficiary)) {
            $this->beneficiary[] = $beneficiary;
            $beneficiary->setTenant($this);
        }

        return $this;
    }

    public function removeBeneficiary(Beneficiary $beneficiary): self
    {
        if ($this->beneficiary->removeElement($beneficiary)) {
            // set the owning side to null (unless already changed)
            if ($beneficiary->getTenant() === $this) {
                $beneficiary->setTenant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AttendanceSheet[]
     */
    public function getAttendanceSheet(): Collection
    {
        return $this->attendanceSheet;
    }

    public function addAttendanceSheet(AttendanceSheet $attendanceSheet): self
    {
        if (!$this->attendanceSheet->contains($attendanceSheet)) {
            $this->attendanceSheet[] = $attendanceSheet;
            $attendanceSheet->setTenant($this);
        }

        return $this;
    }

    public function removeAttendanceSheet(AttendanceSheet $attendanceSheet): self
    {
        if ($this->attendanceSheet->removeElement($attendanceSheet)) {
            // set the owning side to null (unless already changed)
            if ($attendanceSheet->getTenant() === $this) {
                $attendanceSheet->setTenant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|orientationSheet[]
     */
    public function getOrientationSheet(): Collection
    {
        return $this->orientationSheet;
    }

    public function addOrientationSheet(orientationSheet $orientationSheet): self
    {
        if (!$this->orientationSheet->contains($orientationSheet)) {
            $this->orientationSheet[] = $orientationSheet;
            $orientationSheet->setTenant($this);
        }

        return $this;
    }

    public function removeOrientationSheet(orientationSheet $orientationSheet): self
    {
        if ($this->orientationSheet->removeElement($orientationSheet)) {
            // set the owning side to null (unless already changed)
            if ($orientationSheet->getTenant() === $this) {
                $orientationSheet->setTenant(null);
            }
        }

        return $this;
    }

    
    /**
     * @return Collection|Holiday[]
     */
    public function getHoliday(): Collection
    {
        return $this->holiday;
    }

    public function addHoliday(Holiday $holiday): self
    {
        if (!$this->holiday->contains($holiday)) {
            $this->holiday[] = $holiday;
            $holiday->setTenant($this);
        }

        return $this;
    }

    public function removeHoliday(Holiday $holiday): self
    {
        if ($this->holiday->removeElement($holiday)) {
            // set the owning side to null (unless already changed)
            if ($holiday->getTenant() === $this) {
                $holiday->setTenant(null);
            }
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function hasRelations() {
        if (
            !$this->getUser()->isEmpty()
        ||  !$this->getSocialWorker()->isEmpty()
        ||  !$this->getEducator()->isEmpty()
        ||  !$this->getActivity()->isEmpty()
        ||  !$this->getPlanning()->isEmpty()
        ||  !$this->getPlace()->isEmpty()
        ||  !$this->getEtructure()->isEmpty()
        ||  !$this->getBeneficiary()->isEmpty()
        ||  !$this->getAttendanceSheet()->isEmpty()
        ||  !$this->getOrientationSheet()->isEmpty()
        ||  !$this->getHoliday()->isEmpty()
        ){
            return true;
        }
    } 

}
