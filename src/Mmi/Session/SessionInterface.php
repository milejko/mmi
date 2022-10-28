<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Session;

interface SessionInterface
{
    /**
     * Constructor (depends on configuration)
     */
    public function __construct(SessionConfig $config);

    /**
     * Session start
     */
    public function start(): void;

    /**
     * Set session id
     */
    public function setId(string $id): void;

    /**
     * Gets session id
     */
    public function getId(): string;

    /**
     * Gets numeric projection of session id
     */
    public function getNumericId(): int;

    /**
     * Destroys session
     */
    public function destroy(): void;

    /**
     * Regenerates session id
     */
    public function regenerateId(bool $deleteOldSession = true): void;
}
