<?php
declare(strict_types=1);

namespace Mmi\Doctrine;

/**
 * Class DoctrineConfig
 * Package Mmi\Doctrine
 */
class DoctrineConfig
{
    /** @var string */
    public $host;

    /** @var int */
    public $port = 3306;

    /** @var string */
    public $dbName;

    /** @var string */
    public $username;

    /** @var string|null */
    public $password;

    /**
     * @var string
     *
     * Namespace that proxy classes use, normally nothing to do with
     */
    public $proxyNamespace = 'App\\Proxy';

    /**
     * @var string
     *
     * @see \Doctrine\DBAL\Driver
     * @see \Doctrine\DBAL\Driver\Mysqli\Driver
     */
    public $databaseDriverClassName;

    /**
     * @var string
     *
     * @see \Doctrine\ORM\Mapping\NamingStrategy
     * @see \Doctrine\ORM\Mapping\DefaultNamingStrategy
     * @see \Doctrine\ORM\Mapping\UnderscoreNamingStrategy
     */
    public $namingStrategy = \Doctrine\ORM\Mapping\UnderscoreNamingStrategy::class;
}
