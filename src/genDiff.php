<?php

namespace Differ\GenDiff;

function genDiff($pathFile1, $pathFile2)
{
    $file1 = json_decode(file_get_contents($pathFile1), true);
    $file2 = json_decode(file_get_contents($pathFile2), true);
    ksort($file1);
    ksort($file2);
    $difference = '';

    array_walk($file1, function ($value, $key) use (&$file2, &$difference) {
        if (array_key_exists($key, $file2)) {
            if ($value !== $file2[$key]) {
                $difference .= '- ' . $key . ': ' . var_export($value, true) . '\n';
                $difference .= '+ ' . $key . ': ' . var_export($file2[$key], true) . '\n';
                $file2[$key] = null;
            } else {
                $difference .= '  ' . $key . ': ' . var_export($value, true) . '\n';
                $file2[$key] = null;
            }
        } else {
            $difference .= '- ' . $key . ': ' . var_export($value, true) . '\n';
        }
    }
    );

    $newItems = array_filter($file2, fn($n) => !is_null($n));

    array_walk($newItems, function ($value, $key) use (&$difference) {
        $difference .= '+ ' . $key . ': ' . var_export($value, true) . '\n';
    }
    );

    return $difference;
}