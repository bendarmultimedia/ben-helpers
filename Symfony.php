<?php

namespace App;

use Exception;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;

class Symfony
{
    private $container;
    public $entity;
    private static $entityNamespace = "App\\Entity\\";
    private static $repositoryNamespace = "App\\Repository\\";
    private $manager;
    public function __construct()
    {
        $dotEnvFilePath = dirname(__DIR__) . '/../panel/.env';
        $this->kernel = $this->implementSymfony($dotEnvFilePath);
        $this->kernel->boot();
        $this->setKernelAttributes();
    }


    public function setKernelAttributes()
    {
        $this->container = $this->kernel->getContainer();
        $this->manager = $this->container->get('doctrine.orm.entity_manager');
    }

    private function implementSymfony($envFilePath)
    {
        (new Dotenv())->bootEnv($envFilePath);

        if ($_SERVER['APP_DEBUG']) {
            umask(0000);
            Debug::enable();
        }
        return  new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
    }


    public function find(string $entityName, int $id)
    {
        return $this->getRepository($entityName)->find($id);
    }

    public function findAll(string $entityName)
    {
        return $this->getRepository($entityName)->findAll();
    }

    public function findBy(string $entityName, array $attributes)
    {
        return $this->getRepository($entityName)->findBy($attributes);
    }
    public function findOneBy(string $entityName, array $attributes)
    {

        return $this->getRepository($entityName)->findOneBy($attributes);
    }

    public function getRepository(string $entityName)
    {
        $repoName = self::$entityNamespace . $entityName;
        return $this->manager->getRepository($repoName);
    }

    public function sendItemToDatabase($item = null, $flush = true, $return_response = true)
    {
        if ($item) {
            try {
                $this->manager->persist($item);
                if ($flush) {
                    $this->manager->flush();
                    return $item->getId();
                }
            } catch (\Throwable $th) {
                return $th;
            }
        } else {
            throw new Exception('Błąd podczas dodawania do bazy danych');
            return false;
        }
    }

    /**
     * Get the Symfony of container
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function getServiceByAlias(string $alias)
    {
        return $this->container->get($alias);
    }

    public function getKernel()
    {
        return $this->kernel;
    }
}
