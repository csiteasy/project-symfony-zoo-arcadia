<?php
namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\String\Slugger\SluggerInterface;



class GlobalBeforeSaveListener implements EventSubscriber
{

    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;
    private Security $security;

    public function __construct(SluggerInterface $slugger,EntityManagerInterface $entityManager,Security $security)
    {
        $this->slugger = $slugger;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityClassName = get_class($entity);
        $entityName = $this->extractEntityName($entityClassName);

        // Vérifiez si l'entité a une propriété "name" et "slug"
        if (method_exists($entity, 'getName') && method_exists($entity, 'getSlug')) {

            $name = $entity->getName();
            if ($name ) { //&& !$slug
                $entity->setSlug($this->generateSlug($entity,$entityClassName));
            }
        }

        // user_id automatique
        if (method_exists($entity, 'getUser') && method_exists($entity, 'setUser')) {
            //dd($entity);
            //$user = $entity->getUser();
            if (!$entity->getId() && $this->security->getUser()) {

                // Récupère l'utilisateur connecté
                $connectedUser = $this->security->getUser();

                // Assigne l'utilisateur connecté à l'entité
                $entity->setUser($connectedUser);


            }
        }


        $metaData =   $this->entityManager->getClassMetadata($entityClassName);
        $associations = $metaData->getAssociationNames();


        foreach ($associations as $association) {
            $relatedEntityClass = $metaData->getAssociationTargetClass($association);

            if (method_exists($relatedEntityClass, 'getCounter'.Ucfirst($entityName))) {

                $relatedEntityName = $this->extractEntityName($relatedEntityClass);

                if (method_exists($args, 'getEntityChangeSet')) {
                    $changeSet = $args->getEntityChangeSet();
                    if ($changeSet && isset($changeSet[$relatedEntityName])) {
                        //dump($changeSet[$entityName]); // valeur avant celles qui seront sauvegardée
                        $oldValue = $changeSet[$relatedEntityName][0]; // Valeur précédente de l'association
                        $newValue = $changeSet[$relatedEntityName][1]; // Nouvelle valeur de l'association
                        if ($oldValue) {
                            $oldEntity = $this->entityManager->getRepository($relatedEntityClass)->find($oldValue);
                            if ($oldEntity) {
                                $decrementMethod = 'decrementCounter' . ucfirst($entityName);
                                if (method_exists($oldEntity, $decrementMethod)) {
                                    $oldEntity->$decrementMethod(); // Appel dynamique de la méthode
                                }
                            }
                        }
                        $newEntity = $this->entityManager->getRepository($relatedEntityClass)->find($newValue);
                        if ($newEntity) {
                            $incrementMethod = 'incrementCounter' . ucfirst($entityName);
                            if (method_exists($newEntity, $incrementMethod)) {
                                $newEntity->$incrementMethod();
                            }
                        }

                    }
                }else{
                    $relatedEntity = $metaData->getFieldValue($entity, $association);

                    if ($relatedEntity) {
                        $relatedEntityClass = $metaData->getAssociationTargetClass($association);

                        if (method_exists($relatedEntityClass, 'getCounter' . ucfirst($entityName))) {
                            $incrementMethod = 'incrementCounter' . ucfirst($entityName);

                            if (method_exists($relatedEntity, $incrementMethod)) {
                                $relatedEntity->$incrementMethod();
                            }
                        }
                    }
                }

            }
        }

    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->prePersist($args);
    }

    private function generateSlug($entity,string $entityClassName): string
    {
        $baseSlug = $this->slugger->slug($entity->getName())->lower()->toString();

        // Récupérer le nom de l'entité
        $entityMetadata = $this->entityManager->getClassMetadata($entityClassName);
        $entityName = $entityMetadata->getName();


        // Vérifier si le slug de base existe déjà dans la base de données pour cette entité
        $repository = $this->entityManager->getRepository($entityName);
        $existingSlug = $repository->findOneBy(['slug' => $baseSlug]);

        // Si le slug existe déjà, ajouter un timestamp pour le rendre unique
        if ($existingSlug && $existingSlug->getId() !== $entity->getId() ){
            $timestamp = time();
            $uniqueSlug = $baseSlug . '-' . $timestamp;
        } else {
            $uniqueSlug = $baseSlug;
        }

        return $uniqueSlug;
    }
    private function extractEntityName($className): string
    {
        // Trouve la position du dernier antislash pour isoler le nom simple de l'entité
        $lastSlashPos = strrpos($className, '\\');
        // Extraire le nom après le dernier antislash et convertir en minuscules
        return strtolower(substr($className, $lastSlashPos + 1));
    }
}
