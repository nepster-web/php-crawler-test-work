<?php

namespace Tests\Application\Unit\Infrastructure\Report;

use App\Infrastructure\Report\ReportNameGenerator;

/**
 * Class ReportNameGeneratorTest
 *
 * @package Tests\Application\Unit\Infrastructure\Report
 */
class ReportNameGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function testGenerateName(): void
    {
        $reportName = ReportNameGenerator::generateName();
        $expected = 'report_' . date('d.m.Y') . '.html';

        $this->assertEquals($expected, $reportName);
    }

    /** @test */
    public function testGenerateNameWithPrefix(): void
    {
        $reportName = ReportNameGenerator::generateName('myPrefix');
        $expected = 'myPrefix_' . date('d.m.Y') . '.html';

        $this->assertEquals($expected, $reportName);
    }

    /** @test */
    public function testGenerateNameWithDomain(): void
    {
        $reportName = ReportNameGenerator::generateName('http://myDomain.com');
        $expected = 'myDomain.com_' . date('d.m.Y') . '.html';

        $this->assertEquals($expected, $reportName);
    }
}
