<?php

namespace App\Entity\Sport;

use App\Entity\Core\City;
use App\Entity\Tenant\Tenant;
use App\Repository\BeneficiaryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BeneficiaryRepository::class)
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Beneficiary
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
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=20)
     * @Gedmo\Versioned()
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=50)
     * @Gedmo\Versioned()
     */
    private $familySituation;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $numberChildren;

    /**
     * @ORM\Column(type="date")
     * @Gedmo\Versioned()
     */
    private $dateBirth;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $address;

    
    /**
     * @ORM\Column(type="string", length=50)
     * @Gedmo\Versioned()
     */
    private $lodging;

    /**
     * @ORM\Column(type="string", length=50)
     * @Gedmo\Versioned()
     */
    private $medicalCover;

    /**
     * @ORM\Column(type="string", length=50)
     * @Gedmo\Versioned()
     */
    private $resourcesReceived;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="beneficiaries")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=13)
     * @Gedmo\Versioned()
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $email;

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
     * @ORM\OneToMany(targetEntity=OrientationSheet::class, mappedBy="beneficiary")
     */
    private $orientationSheets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $autreResourcesReceived;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $autreLodging;

    /**
     * @ORM\ManyToOne(targetEntity=Tenant::class, inversedBy="beneficiary")
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getFamilySituation(): ?string
    {
        return $this->familySituation;
    }

    public function setFamilySituation(string $familySituation): self
    {
        $this->familySituation = $familySituation;

        return $this;
    }

    public function getNumberChildren(): ?int
    {
        return $this->numberChildren;
    }

    public function setNumberChildren(int $numberChildren): self
    {
        $this->numberChildren = $numberChildren;

        return $this;
    }

    public function getDateBirth(): ?\DateTimeInterface
    {
        return $this->dateBirth;
    }

    public function setDateBirth(\DateTimeInterface $dateBirth): self
    {
        $this->dateBirth = $dateBirth;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLodging(): ?string
    {
        return $this->lodging;
    }

    public function setLodging(string $lodging): self
    {
        $this->lodging = $lodging;

        return $this;
    }

    public function getMedicalCover(): ?string
    {
        return $this->medicalCover;
    }

    public function setMedicalCover(string $medicalCover): self
    {
        $this->medicalCover = $medicalCover;

        return $this;
    }

    public function getResourcesReceived(): ?string
    {
        return $this->resourcesReceived;
    }

    public function setResourcesReceived(string $resourcesReceived): self
    {
        $this->resourcesReceived = $resourcesReceived;

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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
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
            $orientationSheet->setBeneficiary($this);
        }

        return $this;
    }

    public function removeOrientationSheet(OrientationSheet $orientationSheet): self
    {
        if ($this->orientationSheets->removeElement($orientationSheet)) {
            // set the owning side to null (unless already changed)
            if ($orientationSheet->getBeneficiary() === $this) {
                $orientationSheet->setBeneficiary(null);
            }
        }

        return $this;
    }

    public function getAutreResourcesReceived(): ?string
    {
        return $this->autreResourcesReceived;
    }

    public function setAutreResourcesReceived(?string $autreResourcesReceived): self
    {
        $this->autreResourcesReceived = $autreResourcesReceived;

        return $this;
    }

    public function getAutreLodging(): ?string
    {
        return $this->autreLodging;
    }

    public function setAutreLodging(?string $autreLodging): self
    {
        $this->autreLodging = $autreLodging;

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
