<?php

namespace Differ\Phpunit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    /** 
    * @dataProvider dataProvider
    */
    public function testGenDiff($format, $path1, $path2, $expectedResultPath): void
    {
        $expectedResult = file_get_contents($expectedResultPath);
        $this->assertEquals(
            $expectedResult,
            genDiff($path1, $path2, $format)
        );
    }

    public function dataProvider() : array
    {
        $jsonFilePath1 = 'tests/fixtures/file1.json';
        $jsonFilePath2 = 'tests/fixtures/file2.json';
        return [
            ['stylish', 'tests/fixtures/file1.yml', 'tests/fixtures/file2.yml',
            $this -> getFixturePath('rightStylishResult1.txt')],
            ['stylish', $jsonFilePath1, $jsonFilePath2,
            $this -> getFixturePath('rightStylishResult2.txt')],
            ['plain', $jsonFilePath1, $jsonFilePath2,
            $this -> getFixturePath('rightPlainResult1.txt')],
            ['json', $jsonFilePath1, $jsonFilePath2,
            $this -> getFixturePath('rightJsonResult1.txt')]
        ];
    }
    private function getFixturePath(string $fileName)
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, "fixtures", $fileName]);
    }
}
