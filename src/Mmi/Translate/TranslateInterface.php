<?php

namespace Mmi\Translate;

/**
 * Translate interface
 */
interface TranslateInterface
{
    /**
     * Adds translation file
     */
    public function addTranslationFile(string $sourceFile, string $locale): self;

    /**
     * Sets locale
     */
    public function setLocale(string $locale): self;

    /**
     * Gets locale
     */
    public function getLocale(): ?string;

    /**
     * Translate string using sprintf notation
     * ->translate('number %d', [12]) returns "number 12"
     */
    public function translate(string $key, array $params = []): string;
}
