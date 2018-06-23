<?php

namespace App\Infrastructure\Validation;

use InvalidArgumentException;

/**
 * Class SimpleExceptionValidator
 * 
 * @package App\Infrastructure\Validation
 */
class SimpleExceptionValidator implements \App\Infrastructure\Contract\Validator
{
    /**
     * @var array 
     */
    private $data = [];
    
    /**
     * SimpleExceptionValidator constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function validate(): void
    {
        if (empty(trim($this->data['url']))) {
            throw new InvalidArgumentException('Param "URL" must be set for the parsing running.');
        }

        if (filter_var($this->data['url'], FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Param "URL" with value "' . $this->data['url'] . '" does\'t a valid URL.');
        }

        if (ctype_digit(strval($this->data['depth'])) === false) {
            throw new InvalidArgumentException('Param "DEPTH" must be integer.');
        }
    }
}
