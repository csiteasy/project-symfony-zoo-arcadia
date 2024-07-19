<?php

namespace App\Entity;

use App\Entity\Traits\DateTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\RaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Race
{
    use UuidTrait;
    use DateTrait;

    #[ORM\Column(length: 50)]
    #[Groups(['default','api_list_lite','api_view_animal'])]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false,options: ['default' => 0])]
    #[Groups(['default'])]
    private ?int $counterAnimal  = 0;

    /**
     * @var Collection<int, Animal>
     */
    #[ORM\OneToMany(targetEntity: Animal::class, mappedBy: 'race')]
    private Collection $animals;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
    }


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


    /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->setRace($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        if ($this->animals->removeElement($animal)) {
            if ($animal->getRace() === $this) {
                $animal->setRace(null);
            }
        }

        return $this;
    }
/*
 *
 */

    public function getCounterAnimal(): ?int
    {
        return $this->counterAnimal;
    }
    public function setCounterAnimal($counter)
    {
        $this->counterAnimal = $counter;
    }
    public function incrementCounterAnimal()
    {

        $this->counterAnimal++;
    }
    public function decrementCounterAnimal()
    {
        if ($this->counterAnimal > 0) {
            $this->counterAnimal--;
        }
    }
}
