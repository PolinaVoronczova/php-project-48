<?php

namespace Differ\Differ;

use function Differ\Parser\parse;
use function Differ\Formater\getFormated;
use function Functional\sort as f_sort;

function genDiff(string $pathFile1, string $pathFile2, string $format = 'stylish')
{
    [$file1, $file2] = parse($pathFile1, $pathFile2);
    if ($file1 == false && $file2 == false) {
        return "file 1 and file 2 not found.\n";
    } elseif ($file1 == false) {
        return "file 1 not found.\n";
    } elseif ($file2 == false) {
        return "file 2 not found.\n";
    }
    $result = buildDiff($file1, $file2);
    return getFormated($result, $format);
}

function buildDiff(array $file1, array $file2)
{
    $merge = array_merge($file1, $file2);
    $mergeKeys = f_sort(array_keys($merge), fn ($left, $right) => strcmp($left, $right));
    $result = array_map(function ($key) use ($file1, $file2) {
        if (key_exists($key, $file1) && key_exists($key, $file2)) {
            if (is_array($file1[$key]) && is_array($file2[$key])) {
                return
                [
                    'key' => $key,
                    'status' => 'array',
                    'children' => buildDiff($file1[$key], $file2[$key])
                ];
            } elseif ($file1[$key] === $file2[$key]) {
                return
                [
                    'key' => $key,
                    'status' => 'no change',
                    'value' => $file1[$key]
                ];
            } elseif ($file1[$key] !== $file2[$key]) {
                if (is_array($file1[$key])) {
                    return
                    [
                        'key' => $key,
                        'status' => 'update array',
                        'oldValue' => buildDiff($file1[$key], $file1[$key]),
                        'newValue' => $file2[$key]
                    ];
                } elseif (is_array($file2[$key])) {
                    return
                    [
                        'key' => $key,
                        'status' => 'update array',
                        'oldValue' => $file1[$key],
                        'newValue' => buildDiff($file2[$key], $file2[$key])
                    ];
                } else {
                    return
                    [
                        'key' => $key,
                        'status' => 'update',
                        'oldValue' => $file1[$key],
                        'newValue' => $file2[$key]
                    ];
                }
            }
        } elseif (key_exists($key, $file1) && !key_exists($key, $file2)) {
            if (is_array($file1[$key])) {
                return
                [
                'key' => $key,
                'status' => 'delete array',
                'children' => buildDiff($file1[$key], $file1[$key])
                ];
            } else {
                return
                [
                'key' => $key,
                'status' => 'delete',
                'value' => $file1[$key]
                ];
            }
        } elseif (!key_exists($key, $file1) && key_exists($key, $file2)) {
            if (is_array($file2[$key])) {
                return
                [
                'key' => $key,
                'status' => 'add array',
                'children' => buildDiff($file2[$key], $file2[$key])
                ];
            } else {
                return
                [
                'key' => $key,
                'status' => 'add',
                'value' => $file2[$key]
                ];
            }
        }
    }, $mergeKeys);
    return $result;
}
