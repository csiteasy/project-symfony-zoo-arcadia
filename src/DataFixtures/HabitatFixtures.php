<?php
namespace App\DataFixtures;

use App\Entity\HabitatImage;
use App\Entity\Habitat;
use App\Service\ImageHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class HabitatFixtures extends Fixture
{

    private ImageHelper $imageHelper;
    private string $imagesDirectory;

    public function __construct(ImageHelper $imageHelper,ParameterBagInterface $params)
    {
        $this->imageHelper = $imageHelper;
        $this->imagesDirectory = $params->get('images_directory').'/habitat';
        $this->clearDirectory($this->imagesDirectory);
        $this->initializeImagesDirectory($this->imagesDirectory);
    }

    public function load(ObjectManager $manager)
    {

        $habitats = [
            ['Savane', ["savane-panorama.jpg","savane-2.jpg"], "La savane est un vaste habitat ouvert, principalement composé de hautes herbes et parsemé de quelques arbres isolés, comme les acacias et les baobabs. Elle se distingue par des saisons bien marquées, alternant entre périodes sèches et humides. Pendant la saison des pluies, la savane se transforme en un paysage verdoyant et luxuriant, tandis que la saison sèche voit la végétation se dessécher et le sol se craqueler. Cet écosystème est le théâtre de migrations spectaculaires, comme celle des gnous, et abrite une faune diversifiée incluant de grands herbivores comme les éléphants, les girafes, les zèbres et les antilopes. Ces animaux sont souvent poursuivis par des prédateurs tels que les lions, les guépards et les hyènes. Les savanes sont également des habitats importants pour de nombreuses espèces d'oiseaux, de reptiles et d'insectes, contribuant à la complexité et à la richesse de cet environnement."],
            ['Jungle',[], "La jungle, ou forêt tropicale, est un environnement luxuriant et dense, caractérisé par une biodiversité exceptionnelle et un climat chaud et humide tout au long de l'année. Les jungles se trouvent principalement autour de l'équateur, couvrant des régions d'Amérique du Sud, d'Afrique centrale et d'Asie du Sud-Est. Elles sont composées de plusieurs strates de végétation, depuis le sol forestier sombre et humide, couvert de feuilles mortes et abritant de nombreuses espèces de champignons et d'insectes, jusqu'au couvert forestier élevé, où les arbres géants comme les kapokiers et les ceibas s'élèvent vers le ciel. Ce type de forêt abrite une multitude d'espèces végétales et animales, allant des imposants arbres géants aux fougères et lianes, en passant par une faune variée comprenant des primates comme les gorilles et les orangs-outans, des oiseaux colorés tels que les aras et les toucans, une multitude d'insectes, des reptiles comme les anacondas et les caméléons, et des mammifères nocturnes comme les chauves-souris. Les jungles jouent un rôle crucial dans la régulation du climat global et sont des réservoirs indispensables de biodiversité."],
            ['Marais',[], "Le marais est un habitat humide et marécageux, souvent caractérisé par des eaux peu profondes et une végétation dense et variée. Ce milieu riche en nutriments offre un refuge à une grande diversité de plantes aquatiques comme les roseaux, les nénuphars et les quenouilles, ainsi que des plantes terrestres adaptées aux conditions humides. Les marais sont souvent bordés de saules et d'aulnes, dont les racines stabilisent le sol et préviennent l'érosion. Ce type d'écosystème abrite une faune variée et abondante. Les marais sont des zones cruciales pour de nombreuses espèces d'oiseaux, notamment les hérons, les canards et les grues, qui trouvent dans cet environnement des lieux de nidification et d'alimentation. Les amphibiens, tels que les grenouilles et les salamandres, prospèrent dans les marais, tout comme de nombreux reptiles comme les tortues et les alligators. Les poissons, tels que les brochets et les carpes, ainsi que des insectes comme les libellules et les moustiques, jouent également un rôle essentiel dans cet écosystème. Les marais sont vitaux pour la filtration naturelle de l'eau, la protection contre les inondations et le maintien de la qualité des sols environnants."],

            /*['Montagne',[], 'Un habitat rude et sec, caractérisé par des altitudes élevées et des températures souvent froides.'],
            ['Désert',[], 'Hostile chaud en journée et froid la nuit, avec peu de précipitations et une végétation clairsemée.'],
            ['Forêt',[], 'Un habitat densément boisé, abritant une grande diversité de flore et de faune.'],
            ['Océan',[], 'Un vaste étendu d\'eau salée qui abrite une immense diversité de vie marine.'],
            ['Plage',[], 'Un habitat côtier de sable ou de galets, où la terre rencontre l\'océan ou la mer.'],
            ['Banquise',[], 'Un habitat glacé et hostile, constitué de couches de glace flottantes sur les océans polaires.'],
            ['Rivière',[], 'Un cours d\'eau douce qui coule de manière continue vers un océan, un lac ou une autre rivière.'],
            ['Lac',[], 'Une grande étendue d\'eau douce entourée de terres, souvent riche en vie aquatique.'],
            ['Grotte',[], 'Un espace souterrain creusé dans la roche, souvent abritant des espèces spécifiques adaptées à l\'obscurité.'],
            ['Volcan',[], 'Un habitat autour d\'un volcan actif ou inactif, caractérisé par des sols riches en minéraux.'],
            ['Plaine',[], 'Une vaste étendue de terrain plat ou légèrement vallonné, souvent utilisée pour l\'agriculture.'],
            ['Mangrove',[], 'Un habitat côtier tropical avec des arbres adaptés aux conditions salines, offrant un refuge à de nombreuses espèces.'],
            ['Canyon',[], 'Un profond ravin creusé par l\'érosion, souvent spectaculaire avec des parois abruptes.'],
            ['Steppe',[], 'Une vaste étendue de plaines herbeuses semi-arides, souvent sujettes à des températures extrêmes.'],
            ['Toundra',[], 'Un habitat froid avec des sols gelés en permanence, supportant une végétation basse et peu d\'arbres.']
      */  ];

        foreach ($habitats as $data) {
            $habitat=new Habitat();
            $habitat->setName($data[0]);
            $habitat->setDescription($data[2]);

            if (isset($data[1]) && is_array($data[1])) { // si une image est définie image dans src/DataFixtures/Images

                foreach ($data[1] as $imageImported) {
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
                        $image = new habitatImage();
                        $image->setExtension($fileInfo['extension']);
                        $image->setFilename($fileInfo['filename']);
                        $habitat->addImage($image);
                        $manager->persist($image);
                    }
                    $manager->persist($habitat);



                }
            }


            $manager->persist($habitat);
        }
        $manager->flush();
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
}
