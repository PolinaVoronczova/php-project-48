<?php

namespace Differ\GenDiff;

use function Differ\Parser\parser;
use function Differ\Formater\getFormated;

function genDiff($pathFile1, $pathFile2, $format)
{
    [$file1, $file2] = parser($pathFile1, $pathFile2);
    $result = getBuildDiff($file1, $file2, $format);
    return getFormated($result, $format);
}

function getBuildDiff($file1, $file2, $format)
{
    $merge = array_merge($file1, $file2);
    ksort($merge);
    $mergeKeys = array_keys($merge);
    $result = array_map(function ($key) use ($file1, $file2, $format) {
        if (key_exists($key, $file1) && key_exists($key, $file2)) {
            if (is_array($file1[$key]) && is_array($file2[$key])) {
                return
                [
                    'key' => $key,
                    'status' => 'array',
                    'children' => getBuildDiff($file1[$key], $file2[$key], $format)
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
                        'oldValue' => getBuildDiff($file1[$key], $file1[$key], $format),
                        'newValue' => $file2[$key]
                    ];
                } elseif (is_array($file2[$key])) {
                    return
                    [
                        'key' => $key,
                        'status' => 'update array',
                        'oldValue' => $file1[$key],
                        'newValue' => getBuildDiff($file2[$key], $file2[$key], $format)
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
                'children' => getBuildDiff($file1[$key], $file1[$key], $format)
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
                'children' => getBuildDiff($file2[$key], $file2[$key], $format)
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
