<?php
declare(strict_types=1);

namespace Mmi\Orm;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\NamingStrategy;

/**
 * Class DoctrineFactory
 * Package Mmi\App
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class DoctrineFactory
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $dbName;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var bool */
    private $dev;

    /** @var string */
    private $databaseDriverClassName;

    /** @var MappingDriver */
    private $mappingDriver;

    /** @var CacheProvider */
    private $cacheDriver;

    /** @var string */
    private $proxyDir;

    /** @var string */
    private $proxyNamespace = 'Proxy';

    /** @var NamingStrategy */
    private $namingStrategy;

    public function __construct(string $host, int $port, string $dbName, string $username, string $password = null, bool $dev = false)
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->dbName   = $dbName;
        $this->username = $username;
        $this->password = $password;
        $this->dev      = $dev;
    }

    /**
     * @param MappingDriver $mappingDriver
     *
     * @return DoctrineFactory
     */
    public function setMappingDriver(MappingDriver $mappingDriver): DoctrineFactory
    {
        $this->mappingDriver = $mappingDriver;

        return $this;
    }

    /**
     * @param CacheProvider $cacheDriver
     *
     * @return DoctrineFactory
     */
    public function setCacheDriver(CacheProvider $cacheDriver): DoctrineFactory
    {
        $this->cacheDriver = $cacheDriver;

        return $this;
    }

    /**
     * @param string $proxyDir
     *
     * @return DoctrineFactory
     */
    public function setProxyDir(string $proxyDir): DoctrineFactory
    {
        $this->proxyDir = $proxyDir;

        return $this;
    }

    /**
     * @param string $proxyNamespace
     *
     * @return DoctrineFactory
     */
    public function setProxyNamespace(string $proxyNamespace): DoctrineFactory
    {
        $this->proxyNamespace = $proxyNamespace;

        return $this;
    }

    /**
     * @param NamingStrategy $namingStrategy
     *
     * @return DoctrineFactory
     */
    public function setNamingStrategy(NamingStrategy $namingStrategy): DoctrineFactory
    {
        $this->namingStrategy = $namingStrategy;

        return $this;
    }

    /**
     * @param string $databaseDriverClassName
     *
     * @return DoctrineFactory
     */
    public function setDatabaseDriverClassName(string $databaseDriverClassName): DoctrineFactory
    {
        $this->databaseDriverClassName = $databaseDriverClassName;

        return $this;
    }

    public function create(): EntityManager
    {
        $configuration = new Configuration();
        $configuration->setMetadataDriverImpl($this->mappingDriver);
        $configuration->setProxyDir($this->proxyDir);
        $configuration->setProxyNamespace($this->proxyNamespace);
        $configuration->setAutoGenerateProxyClasses($this->dev);
        $configuration->setMetadataCacheImpl($this->cacheDriver);
        $configuration->setQueryCacheImpl($this->cacheDriver);
        $configuration->setNamingStrategy($this->namingStrategy);

        return EntityManager::create(
            [
                'host'        => $this->host,
                'port'        => $this->port,
                'user'        => $this->username,
                'password'    => $this->password,
                'dbname'      => $this->dbName,
                'driverClass' => $this->databaseDriverClassName
            ],
            $configuration
        );
    }
}
