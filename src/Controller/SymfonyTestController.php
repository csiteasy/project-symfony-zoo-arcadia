<?php
namespace App\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\AnimalVisitCounterService;

class SymfonyTestController extends AbstractController
{
    #[Route('/test-db', name: 'test-db')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        try {
            $connection = $entityManager->getConnection();
            $connection->connect();
            if ($connection->isConnected()) {
                $params = $connection->getParams(); // @internal Acceptable pour le test de connection
                $databaseName = $params['dbname'] ?? 'N/A';
                $host = $params['host'] ?? 'N/A';
                $port = $params['port'] ?? 'N/A';
                $username = $params['user'] ?? 'N/A';
                return new Response(
                    "Connected to database '{$databaseName}' on host '{$host}:{$port}' with username '{$username}' successfully."
                );
            } else {
                return new Response("Failed to connect to database.");
            }
        } catch (\Exception $e) {
            return new Response("Failed to connect to database: " . $e->getMessage());
        }
    }
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('sender@example.com')
            ->to('recipient@example.com')
            ->subject('Test Email')
            ->text('This is a test email.')
            ->html('<p>This is a test email.</p>');
        try {
            $mailer->send($email);
            return new Response('Email sent successfully ' . $_ENV['MAILER_DSN']);
        } catch (\Exception $e) {
            return new Response('Failed to send email: ' . $e->getMessage());
        }
    }
    #[Route('/test-mango', name: 'test-mango')]
    public function testMango(DocumentManager $dm): Response
    {


        try {
            // Tentative de connexion à MongoDB
            $client = $dm->getClient();
            $client->selectDatabase($dm->getConfiguration()->getDefaultDB())->listCollections();
            $databaseName = $dm->getConfiguration()->getDefaultDB();
            $database = $client->selectDatabase($databaseName);

           // test SIMPLE
            $collection = $database->selectCollection('animalsTEST');
            $animal = [
                'name' => 'Lion',
                'visit_count' => 1
            ];
            $collection->insertOne($animal);





            return new Response(
                "Connected to MongoDB database '{$databaseName}' successfully."
            );
        } catch (\Exception $e) {
            return new Response("Failed to connect to MongoDB: " . $e->getMessage());
        }
    }
    #[Route('/increment-visit/{name}', name: 'increment-visit')]
    public function incrementVisit(string $name, AnimalVisitCounterService $animalVisitCounterService): Response
    {
        try {
            // Incrémenter le compteur de visites ou créer l'animal si nécessaire
            $counter = $animalVisitCounterService->incrementVisitCount($name);

            return new Response(
                "Visit count for animal " . $counter->getAnimalName() . " incremented to: " . $counter->getVisitCount()
            );
        } catch (\Exception $e) {
            return new Response("Failed to increment visit count: " . $e->getMessage());
        }
    }
}
