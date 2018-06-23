<?php

namespace App\Infrastructure\Factory;

use App\Infrastructure\Contract\Validator;
use App\Infrastructure\Validation\SimpleExceptionValidator;

/**
 * Class ValidatorFactory
 *
 * @package App\Infrastructure\Factory
 */
class ValidatorFactory
{
    /**
     * @param array $data
     * @return Validator
     */
    public function __invoke(array $data): Validator
    {
        return (new SimpleExceptionValidator($data));
    }
}
