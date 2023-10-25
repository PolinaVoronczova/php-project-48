<?php

namespace Differ\Phpunit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $fileYmlPath1 = 'tests/fixtures/file1.yml';
        $fileYmlPath2 = 'tests/fixtures/file2.yml';
        $expectedResult1 = "{\n  - follow: false\n    host: hexlet.io\n  - proxy: 123.234.53.22\n  - timeout: 50\n  + timeout: 20\n  + verbose: true\n}";
        $this->assertEquals($expectedResult1, genDiff($fileYmlPath1, $fileYmlPath2, 'stylish'));

        $filePath1 = 'tests/fixtures/file1.json';
        $filePath2 = 'tests/fixtures/file2.json';
        $expectedResult2 = "{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: 
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}";
        $this->assertEquals($expectedResult2, genDiff($filePath1, $filePath2, 'stylish'));

        $expectedResult3 = "Property 'common.follow' was added with value: false
Property 'common.setting2' was removed
Property 'common.setting3' was updated. From true to null
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: [complex value]
Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
Property 'common.setting6.ops' was added with value: 'vops'
Property 'group1.baz' was updated. From 'bas' to 'bars'
Property 'group1.nest' was updated. From [complex value] to 'str'
Property 'group2' was removed
Property 'group3' was added with value: [complex value]";
        $this->assertEquals($expectedResult3, genDiff($filePath1, $filePath2, 'plain'));

        $expectedResult4 = '[
    {
        "key": "common",
        "status": "array",
        "children": [
            {
                "key": "follow",
                "status": "was added",
                "value": false
            },
            {
                "key": "setting1",
                "status": "no change",
                "value": "Value 1"
            },
            {
                "key": "setting2",
                "status": "was removed",
                "removed value": 200
            },
            {
                "key": "setting3",
                "status": "was updated",
                "old value": true,
                "new value": null
            },
            {
                "key": "setting4",
                "status": "was added",
                "value": "blah blah"
            },
            {
                "key": "setting5",
                "status": "was added array",
                "children": [
                    {
                        "key": "key5",
                        "status": "no change",
                        "value": "value5"
                    }
                ]
            },
            {
                "key": "setting6",
                "status": "array",
                "children": [
                    {
                        "key": "doge",
                        "status": "array",
                        "children": [
                            {
                                "key": "wow",
                                "status": "was updated",
                                "old value": "",
                                "new value": "so much"
                            }
                        ]
                    },
                    {
                        "key": "key",
                        "status": "no change",
                        "value": "value"
                    },
                    {
                        "key": "ops",
                        "status": "was added",
                        "value": "vops"
                    }
                ]
            }
        ]
    },
    {
        "key": "group1",
        "status": "array",
        "children": [
            {
                "key": "baz",
                "status": "was updated",
                "old value": "bas",
                "new value": "bars"
            },
            {
                "key": "foo",
                "status": "no change",
                "value": "bar"
            },
            {
                "key": "nest",
                "status": "was updated array",
                "old value": [
                    {
                        "key": "key",
                        "status": "no change",
                        "value": "value"
                    }
                ],
                "new value": "str"
            }
        ]
    },
    {
        "key": "group2",
        "status": "was removed array",
        "children": [
            {
                "key": "abc",
                "status": "no change",
                "value": 12345
            },
            {
                "key": "deep",
                "status": "array",
                "children": [
                    {
                        "key": "id",
                        "status": "no change",
                        "value": 45
                    }
                ]
            }
        ]
    },
    {
        "key": "group3",
        "status": "was added array",
        "children": [
            {
                "key": "deep",
                "status": "array",
                "children": [
                    {
                        "key": "id",
                        "status": "array",
                        "children": [
                            {
                                "key": "number",
                                "status": "no change",
                                "value": 45
                            }
                        ]
                    }
                ]
            },
            {
                "key": "fee",
                "status": "no change",
                "value": 100500
            }
        ]
    }
]';
            $this->assertEquals($expectedResult4, genDiff($filePath1, $filePath2, 'json'));
    }
}
