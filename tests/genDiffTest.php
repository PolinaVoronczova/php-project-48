<?php

namespace Differ\Test;

use PHPUnit\Framework\TestCase;
use function Differ\GenDiff\genDiff;

class genDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $filePath1 = 'fixtures/file1.json';
        $filePath2 = 'fixtures/file2.json';
        $this->assertEquals('- follow: false\n  host: hexlet.io\n- proxy: 123.234.53.22\n- timeout: 50\n+ timeout: 20\n+ verbose: true\n', genDiff($filePath1, $filePath2));
    }
}