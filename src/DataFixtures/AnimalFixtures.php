<?php
namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\AnimalImage;
use App\Entity\Habitat;
use App\Entity\Race;
use App\Service\ImageHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AnimalFixtures extends Fixture implements DependentFixtureInterface
{

    private ImageHelper $imageHelper;
    private string $imagesDirectory;

    public function __construct(ImageHelper $imageHelper,ParameterBagInterface $params)
    {
        $this->imageHelper = $imageHelper;
        $this->imagesDirectory = $params->get('images_directory').'/animal';
        $this->clearDirectory($this->imagesDirectory);
        $this->initializeImagesDirectory($this->imagesDirectory);
    }
    private function clearDirectory(string $directory): void
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($directory)) {
            try {
                $filesystem->remove($directory);
                $filesystem->mkdir($directory);
            } catch (IOExceptionInterface $exception) {
                throw new \Exception("Erreur lors de la suppression des images: " . $exception->getMessage());
            }
        }
    }
    private function initializeImagesDirectory(string $directory): void
    {
        $filesystem = new Filesystem();
        try {
            if ($filesystem->exists($directory)) {
                $filesystem->remove($directory);
            }
            $filesystem->mkdir($directory);
        } catch (IOExceptionInterface $exception) {
            throw new \Exception("Erreur lors de la gestion du répertoire des images: " . $exception->getMessage());
        }
    }


    public function load(ObjectManager $manager)
    {
        $races = [];
        foreach ($manager->getRepository(Race::class)->findAll() as $race) {
            $races[$race->getSlug()] = $race;
        }

        $habitats = [];
        foreach ($manager->getRepository(Habitat::class)->findAll() as $habitat) {
            $habitats[$habitat->getSlug()] = $habitat;
        }


        $animals = [
            ['Aurora', 'flamant-rose', 'marais',['Aurora.webp','Aurora-2.webp','Aurora-3.webp']],
            ['Fiona', 'flamant-rose', 'marais',['Fiona.webp','Fiona-2.webp','Fiona-3.webp']],
            ['Asha', 'gorille-des-montagnes', 'jungle',['Asha.webp']],
            //['Bao', 'panda-geant', 'montagne',['Baobao.webp','Baobao-2.webp','Baobao-3.webp']],
            ['Belya', 'tigre-de-siberie', 'savane'],
            ['Casper', 'lion-blanc', 'savane'],
            //['Cyrus', 'lynx', 'foret'],
            //['Dawa', 'panthere-des-neiges', 'montagne'],
            //['Delya', 'ours-polaire', 'banquise'],
            //['Djinn', 'panda-roux', 'foret'],
            //['Django', 'loup-arctique', 'banquise'],
            ['Djunga', 'loutre', 'marais'],
            //['Djy', 'puma', 'montagne'],
            //['Djyvy', 'ours-brun', 'foret'],
            //['Nuka', 'ours-polaire', 'banquise'],
            //['Mei Mai', 'panda-roux', 'foret'],
            //['Ghost', 'loup-arctique', 'banquise'],
            ['Otto', 'loutre', 'marais'],
            //['Shade', 'puma', 'montagne'],
            //['Bruno', 'ours-brun', 'foret'],
            //['Snow', 'leopard-des-neiges', 'montagne'],
            ['Simba', 'lion', 'savane'],
            ['Speedy', 'guepard', 'savane'],
            ['Jag', 'jaguar', 'jungle'],
            ['Spot', 'leopard', 'savane'],
            ['Stripes', 'tigre', 'jungle']
        ];

        foreach ($animals as $data) {
            $animal=new Animal();
            $animal->setName($data[0]);
            $animal->setRace($races[$data[1]]);
            $animal->setHabitat($habitats[$data[2]]);
            if (isset($data[3]) && is_array($data[3])) { // si une image est définie image dans src/DataFixtures/Images

              foreach ($data[3] as $imageImported) {
                    $imageFilePath = __DIR__ . '/Images/' . $imageImported;
                    $imageFile = new UploadedFile(
                        $imageFilePath,
                        $imageImported,
                        mime_content_type($imageFilePath),
                        null,
                        true // Mark it as a test file
                    );
                  $fileInfo = $this->imageHelper->prepareFileInfo($imageFile);
                  if ($this->imageHelper->saveImage($imageFile, $fileInfo, $this->imagesDirectory, true)){
                    $image = new AnimalImage();
                    $image->setExtension($fileInfo['extension']);
                    $image->setFilename($fileInfo['filename']);
                    $animal->addImage($image);
                    $manager->persist($image);
                  }




                }
            }
            $manager->persist($animal);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RaceFixtures::class,
            HabitatFixtures::class,
        ];
    }
}
