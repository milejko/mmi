<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2018 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Validator;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\ValidationResult;

/**
 * Walidator numeru telefonu
 *
 * @method self setRegion($region) Zmienia region
 * @method string getRegion() Zwraca region
 */
class Phone extends ValidatorAbstract
{

    /**
     * Komunikat niepoprawnym numerze
     */
    const INVALID = 'Podany numer telefonu jest niepoprawny';

    /**
     * Komunikat o zbyt krótkim numerze telefonu
     */
    const INVALID_TOO_SHORT = 'Podany numer telefonu jest niepoprawny, jest za krótki';

    /**
     * Komunikat o zbyt długim numerze telefonu
     */
    const INVALID_TOO_LONG = 'Podany numer telefonu jest niepoprawny, jest za długi';

    /**
     * Komunikat o nieznanym kodzie kraju
     */
    const INVALID_COUNTRY_CODE = 'Podany numer telefonu jest niepoprawny, zawiera nieznany kod kraju';

    /**
     * @inheritdoc
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        // Domyślny region dla numerów, które nie zawierają prefixu z kodem kraju
        $this->setRegion('PL');
    }

    /**
     * Walidacja numeru telefonu
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        // Próba prasowania numeru
        try {
            $phoneNumber = $phoneNumberUtil->parse($value, $this->getRegion());
        } catch (NumberParseException $e) {
            // Obsługa błędów
            switch ($e->getErrorType()) {
                case NumberParseException::TOO_SHORT_AFTER_IDD:
                case NumberParseException::TOO_SHORT_NSN:
                    return $this->_error(static::INVALID_TOO_SHORT);

                case NumberParseException::TOO_LONG:
                    return $this->_error(static::INVALID_TOO_LONG);

                case NumberParseException::INVALID_COUNTRY_CODE:
                    return $this->_error(static::INVALID_COUNTRY_CODE);

                case NumberParseException::NOT_A_NUMBER:
                    return $this->_error(static::INVALID);
            }

            return $this->_error(static::INVALID);
        }

        // Sprawdzenie poprawności numeru
        if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
            // Próba ustalenia dlaczego numer jest niepoprawny
            $phoneNumberValidationResult = $phoneNumberUtil->isPossibleNumberWithReason($phoneNumber);
            switch ($phoneNumberValidationResult) {
                case ValidationResult::TOO_SHORT:
                    return $this->_error(static::INVALID_TOO_SHORT);

                case ValidationResult::TOO_LONG:
                    return $this->_error(static::INVALID_TOO_LONG);

                case ValidationResult::INVALID_COUNTRY_CODE:
                    return $this->_error(static::INVALID_COUNTRY_CODE);
            }

            return $this->_error(static::INVALID);
        }

        // Poprawny numer
        return true;
    }

}
