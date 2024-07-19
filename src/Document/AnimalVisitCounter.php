<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "animal_visit_counters")]
class AnimalVisitCounter
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $animalName;

    #[MongoDB\Field(type: "int")]
    private $visitCount = 0;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAnimalName(): ?string
    {
        return $this->animalName;
    }

    public function setAnimalName(string $animalName): self
    {
        $this->animalName = $animalName;
        return $this;
    }

    public function getVisitCount(): ?int
    {
        return $this->visitCount;
    }

    public function incrementVisitCount(): self
    {
        $this->visitCount++;
        return $this;
    }
}

