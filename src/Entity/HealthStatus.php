<?php

namespace App\Entity;

use App\Entity\Traits\DateTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\HealthStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: HealthStatusRepository::class)]
#[ORM\HasLifecycleCallbacks] // pour créer les dates de création et de modification
class HealthStatus
{
    use uuidTrait;
    use DateTrait;

    #[ORM\Column(length: 60)]
    #[Groups(['default','api_view_animal'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['default','api_view_animal'])]
    private ?string $description = null;

    /**
     * @var Collection<int, MedicalReport>
     */
    #[ORM\OneToMany(targetEntity: MedicalReport::class, mappedBy: 'healthStatus')]
    private Collection $medicalReports;

   /* #[ORM\Column(type: Types::INTEGER, nullable: false,options: ['default' => 0])]
    #[Groups(['default'])]
    private ?int $counterReport  = 0;*/

    /**
     * @var Collection<int, Report>
     */
   /* #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'healthStatus')]
    private Collection $report;*/

    public function __construct()
    {
        $this->report = new ArrayCollection();
        $this->medicalReports = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReport(): Collection
    {
        return $this->report;
    }

    public function addReport(Report $report): static
    {
        if (!$this->report->contains($report)) {
            $this->report->add($report);
            $report->setHealthStatus($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->report->removeElement($report)) {
            if ($report->getHealthStatus() === $this) {
                $report->setHealthStatus(null);
            }
        }

        return $this;
    }


    public function getCounterReport():int
    {
        return $this->counterReport;
    }
    public function setCounterReport($counter)
    {
        $this->counterReport = $counter;
    }
    public function incrementCounterReport()
    {

        $this->counterReport++;
    }
    public function decrementCounterReport()
    {
        if ($this->counterReport > 0) {
            $this->counterReport--;
        }
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
            $medicalReport->setHealthStatus($this);
        }

        return $this;
    }

    public function removeMedicalReport(MedicalReport $medicalReport): static
    {
        if ($this->medicalReports->removeElement($medicalReport)) {
            // set the owning side to null (unless already changed)
            if ($medicalReport->getHealthStatus() === $this) {
                $medicalReport->setHealthStatus(null);
            }
        }

        return $this;
    }

}
