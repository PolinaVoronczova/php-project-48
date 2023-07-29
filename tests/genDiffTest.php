<?php

namespace Differ\Phpunit\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\GenDiff\genDiff;

class genDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $fileJsonPath1 = 'tests/fixtures/file1.json';
        $fileJsonPath2 = 'tests/fixtures/file2.json';
        $expectedResult1 = "- follow: false\n  host: 'hexlet.io'\n- proxy: '123.234.53.22'\n- timeout: 50\n+ timeout: 20\n+ verbose: true\n";
        $this->assertEquals($expectedResult1, genDiff($fileJsonPath1, $fileJsonPath2));

        $fileYmlPath1 = 'tests/fixtures/file1.yml';
        $fileYmlPath2 = 'tests/fixtures/file2.yml';
        $expectedResult1 = "- follow: false\n  host: 'hexlet.io'\n- proxy: '123.234.53.22'\n- timeout: 50\n+ timeout: 20\n+ verbose: true\n";
        $this->assertEquals($expectedResult1, genDiff($fileYmlPath1, $fileYmlPath2));

        $filePath1 = 'tests/fixtures/file1.yml';
        $filePath2 = 'tests/fixtures/file2.yml';
        $expectedResult1 = "{\n
            common: {\n
              + follow: false\n
                setting1: Value 1\n
              - setting2: 200\n
              - setting3: true\n
              + setting3: null\n
              + setting4: blah blah\n
              + setting5: {\n
                    key5: value5\n
                }\n
                setting6: {\n
                    doge: {\n
                      - wow:\n
                      + wow: so much\n
                    }\n
                    key: value\n
                  + ops: vops\n
                }\n
            }\n
            group1: {\n
              - baz: bas\n
              + baz: bars\n
                foo: bar\n
              - nest: {\n
                    key: value\n
                }\n
              + nest: str\n
            }\n
          - group2: {\n
                abc: 12345\n
                deep: {\n
                    id: 45\n
                }\n
            }\n
          + group3: {\n
                deep: {\n
                    id: {\n
                        number: 45\n
                    }\n
                }\n
                fee: 100500\n
            }\n
        }";
        $this->assertEquals($expectedResult1, genDiff($fileYmlPath1, $fileYmlPath2));
    }
}