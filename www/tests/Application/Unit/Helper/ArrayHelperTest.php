<?php

namespace Tests\Application\Unit\Helper;

use App\Infrastructure\Helper\ArrayHelper;

/**
 * Class ArrayHelperTest
 *
 * @package Tests\Application\Unit\Helper
 */
class ArrayHelperTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function testSortAssociativeArrayByKeyWithOrderByAsc(): void
    {
        $array = [
            ['number' => 1],
            ['number' => 3],
            ['number' => 2],
        ];

        ArrayHelper::sortAssociativeArrayByKey($array, 'number', false);

        $expected = [
            ['number' => 1],
            ['number' => 2],
            ['number' => 3],
        ];

        $this->assertEquals($expected, $array);
    }

    /** @test */
    public function testSortAssociativeArrayByKeyWithOrderByDesc(): void
    {
        $array = [
            ['number' => 1],
            ['number' => 3],
            ['number' => 2],
        ];

        ArrayHelper::sortAssociativeArrayByKey($array, 'number', true);

        $expected = [
            ['number' => 3],
            ['number' => 2],
            ['number' => 1],
        ];

        $this->assertEquals($expected, $array);
    }

    /** @test */
    public function testSortAssociativeArrayByArray(): void
    {
        $array = [
            'three' => 3,
            'two' => 2,
            'five' => 5,
            'four' => 4,
            'one' => 1,
        ];

        ArrayHelper::sortAssociativeArrayByArray($array, [
            'one',
            'two',
            'three',
            'four',
            'five',
        ]);

        $expected = [
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'four' => 4,
            'five' => 5,
        ];

        $this->assertEquals($expected, $array);
    }
}
