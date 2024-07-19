<?php

namespace App\Entity;

use AllowDynamicProperties;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Entity\Race;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\AnimalRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: AnimalRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('name', message: 'Ce nom est déjà utilisé. Veuillez en choisir un autre.')]

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['api_list_lite']]
        ),
        new Get(

            normalizationContext: ['groups' => ['api_view_animal']],

        )
    ]

)]
/*#[ApiResource(
    uriTemplate: '/animals/{habitatSlug}',
    operations: [new GetCollection()],
    uriVariables: [
        'habitatSlug' => new Link(fromProperty: 'animals', fromClass: Habitat::class, identifiers: ['slug'])
    ],
    normalizationContext: ['groups' => ['api_list_lite']]
)]*/
class Animal
{
    use UuidTrait;
    use DateTrait;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['default','api_list_lite','api_view_animal'])]
    private ?string $name = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Groups(['api_list_lite'])]
    #[ApiProperty(identifier: true)]
    private ?string $slug = null;


    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['api_list_lite','api_view_animal'])]
    private ?race $race = null;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['api_view_animal'])]
    private ?Habitat $habitat = null;


    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: AnimalImage::class, fetch: 'EAGER', cascade: ['persist', 'remove'])]
    #[Groups(['default','api_list_lite','api_view_animal'])]
    private Collection $images;

    /**
     * @var Collection<int, MedicalReport>
     */
    #[ORM\OneToMany(targetEntity: MedicalReport::class, mappedBy: 'animal')]
    /*#[Groups(['api_view_animal'])]*/
    private Collection $medicalReports;







    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }
    #[Groups(['default'])]
    public function getRace(): ?race
    {
        return $this->race;
    }
    public function setRace(?race $race): static
    {
        $this->race = $race;
        return $this;
    }
    #[Groups(['default'])]
    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }



    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->medicalReports = new ArrayCollection();

    }
    // Getters and setters for other properties
    #[Groups(['default2'])]
    public function getImages(): Collection
    {
        return $this->images;
    }
    public function addImage(AnimalImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAnimal($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, MedicalReport>
     */
    public function getMedicalReports(): Collection
    {
        return $this->medicalReports;
    }

    public function addMedicalReport(MedicalReport $medicalReport): static
    {
        if (!$this->medicalReports->contains($medicalReport)) {
            $this->medicalReports->add($medicalReport);
            $medicalReport->setAnimal($this);
        }

        return $this;
    }

    public function removeMedicalReport(MedicalReport $medicalReport): static
    {
        if ($this->medicalReports->removeElement($medicalReport)) {
            // set the owning side to null (unless already changed)
            if ($medicalReport->getAnimal() === $this) {
                $medicalReport->setAnimal(null);
            }
        }

        return $this;
    }

    // API PLATFORM
    #[Groups(['api_view_animal'])]
    public function getLastMedicalReport(): ?MedicalReport
    {
        return $this->medicalReports->matching(Criteria::create()
            ->orderBy(['createdAt' => 'DESC'])
            ->setMaxResults(1)
        )->first() ?: null;
    }




}
