<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Filter;

use Mmi\App\KernelException;

/**
 * Pad filter
 * @method self setPadCharacter($parCharacter)
 * @method string setPadCharacter()
 * @method self setLength(int $length)
 * @method int getLength()
 * @method string getPadMethod()
 */
class Pad extends \Mmi\Filter\FilterAbstract
{
    private const DEFAULT_PAD_CHARACTER = ' ';
    private const DEFAULT_LENGTH = 5;
    private const DEFAULT_PAD_METHOD = STR_PAD_RIGHT;

    private $padCharacted = self::DEFAULT_PAD_CHARACTER;
    private $length = self::DEFAULT_LENGTH;
    private $padMethod = self::DEFAULT_PAD_METHOD;

    public function setPadCharacter(string $pathCharacter): self
    {
        $this->padCharacted = $pathCharacter;
        return $this;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function setPadMetod(string $padMethod): self
    {
        switch ($padMethod) {
            case STR_PAD_LEFT:
            case STR_PAD_RIGHT:
            case STR_PAD_BOTH:
                $this->padMethod = $padMethod;
                // no break
            default:
                $this->padMethod = self::DEFAULT_PAD_METHOD;
        }
        return $this;
    }

    public function setPadLeft(): self
    {
        $this->padMethod = STR_PAD_LEFT;
        return $this;
    }

    public function setPadRight(): self
    {
        $this->padMethod = STR_PAD_RIGHT;
        return $this;
    }

    public function setPadBoth(): self
    {
        $this->padMethod = STR_PAD_BOTH;
        return $this;
    }

    /**
     * Zmniejsza wszystkie litery w ciągu
     * @param mixed $value wartość
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    public function filter($value): string
    {
        return str_pad(
            (string) $value,
            $this->length,
            $this->padCharacted,
            $this->padMethod
        );
    }
}
