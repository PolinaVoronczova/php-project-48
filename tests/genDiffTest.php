<?php

namespace Differ\Phpunit\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\GenDiff\genDiff;

class genDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $filePath1 = 'tests/fixtures/file1.json';
        $filePath2 = 'tests/fixtures/file2.json';
        $expectedResult1 = "- follow: false\n  host: 'hexlet.io'\n- proxy: '123.234.53.22'\n- timeout: 50\n+ timeout: 20\n+ verbose: true\n";
        $this->assertEquals($expectedResult1, genDiff($filePath1, $filePath2));
    }
}