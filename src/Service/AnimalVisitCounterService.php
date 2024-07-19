<?php
namespace App\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\AnimalVisitCounter;

class AnimalVisitCounterService
{
    private $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function findCounterByAnimalName(string $animalName): ?AnimalVisitCounter
    {
        return $this->dm->getRepository(AnimalVisitCounter::class)->findOneBy(['animalName' => $animalName]);
    }

    public function createCounter(string $animalName): AnimalVisitCounter
    {
        $counter = new AnimalVisitCounter();
        $counter->setAnimalName($animalName);

        $this->dm->persist($counter);
        $this->dm->flush();

        return $counter;
    }

    public function incrementVisitCount(string $animalName): AnimalVisitCounter
    {
        $counter = $this->findCounterByAnimalName($animalName);

        if (!$counter) {
            $counter = $this->createCounter($animalName);
        }

        $counter->incrementVisitCount();
        $this->dm->flush();

        return $counter;
    }
}

