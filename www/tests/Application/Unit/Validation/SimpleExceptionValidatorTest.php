<?php

namespace Tests\Application\Unit\Validation;

use Exception;
use InvalidArgumentException;
use App\Infrastructure\Validation\SimpleExceptionValidator;

/**
 * Class SimpleExceptionValidatorTest
 *
 * @package Tests\Application\Unit\Validation
 */
class SimpleExceptionValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @inheritdoc
     */
    public function additionCorrectProvider(): array
    {
        return [
            [['http://site.ru', 1]],
            [['https://site.ru', 1]],

            [['http://site.ru', 5]],

            [['http://site.ru', null]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function additionIncorrectProvider(): array
    {
        return [
            [['incorrect domain', 1], 'Param "URL" with value "incorrect domain" does\'t a valid URL.'],
            [['http2://site.ru', 1], 'Param "URL" with value "http2://site.ru" does\'t a valid URL.'],
            [['http://site&.ru', 1], 'Param "URL" with value "http://site&.ru" does\'t a valid URL.'],

            [[null, 1], 'Param "URL" must be set for the parsing running.'],
            [['', 1], 'Param "URL" must be set for the parsing running.'],
            [['   ', 1], 'Param "URL" must be set for the parsing running.'],

            [['http://site.ru', 'depth'], 'Param "DEPTH" must be a positive integer.'],
            [['http://site.ru', -1], 'Param "DEPTH" must be a positive integer.'],
        ];
    }

    /**
     * @dataProvider additionCorrectProvider
     * @param array $data
     */
    public function testValidateWithCorrectData(array $data): void
    {
        $isValid = true;

        try {

            list($url, $depth) = $data;

            $validator = new SimpleExceptionValidator([
                'url' => $url,
                'depth' => $depth,
            ]);

            $validator->validate();

        } catch (Exception $e) {
            $isValid = false;
        }

        $this->assertTrue($isValid);
    }

    /**
     * @dataProvider additionIncorrectProvider
     * @param array $data
     * @param string $expected
     */
    public function testValidateWithIncorrectData(array $data, string $expected): void
    {
        try {

            list($url, $depth) = $data;

            $validator = new SimpleExceptionValidator([
                'url' => $url,
                'depth' => $depth,
            ]);

            $validator->validate();

        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertEquals($expected, $e->getMessage());
        }
    }
}
