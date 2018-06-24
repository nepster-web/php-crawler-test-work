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

        if ($this->validateUrl($this->data['url']) === false) {
            throw new InvalidArgumentException('Param "URL" with value "' . $this->data['url'] . '" does\'t a valid URL.');
        }

        if (isset($this->data['depth']) && ctype_digit(strval($this->data['depth'])) === false) {
            throw new InvalidArgumentException('Param "DEPTH" must be a positive integer.');
        }
    }

    /**
     * @param string $url
     * @return bool
     */
    private function validateUrl(string $url): bool
    {
        $url = trim($url);

        return (
            (strpos($url, "http://") === 0 || strpos($url, "https://") === 0) &&
            filter_var(
                $url,
                FILTER_VALIDATE_URL,
                FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED
            ) !== false
        );
    }

}
