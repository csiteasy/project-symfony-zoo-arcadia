<?php

namespace App\Entity;

use App\Entity\Traits\DateTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\MedicalReportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: MedicalReportRepository::class)]
#[ORM\HasLifecycleCallbacks]
class MedicalReport
{
    use UuidTrait;
    use DateTrait;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['default','api_view_animal'])]
    private ?string $review = null;

    #[ORM\Column]
    #[Groups(['default'])]
    private ?\DateTimeImmutable $visitedAt = null;

    #[ORM\ManyToOne(inversedBy: 'medicalReports')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['default'])]
    private ?Animal $animal = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'medicalReports')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['default'])]
    private User $user;

    #[ORM\ManyToOne(inversedBy: 'medicalReports')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['default','api_view_animal'])]
    private ?HealthStatus $healthStatus = null;



    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): static
    {
        $this->review = $review;

        return $this;
    }

    public function getVisitedAt(): ?\DateTimeImmutable
    {
        return $this->visitedAt;
    }

    public function setVisitedAt(\DateTimeImmutable $visitedAt): static
    {
        $this->visitedAt = $visitedAt;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }
    public function getUser(): User
    {
        return $this->user;
    }
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
    public function getHealthStatus(): ?healthStatus
    {
        return $this->healthStatus;
    }

    public function setHealthStatus(?healthStatus $healthStatus): static
    {
        $this->healthStatus = $healthStatus;

        return $this;
    }

}
