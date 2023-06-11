<?php

namespace GenDiff;

function genDiff($pathFile1, $pathFile2)
{
    var_dump($pathFile1);
    var_dump($pathFile2);
    $file1 = json_decode(file_get_contents($pathFile1),true);
    ksort($file1);
    var_dump($file1);
    $file2 = json_decode(file_get_contents($pathFile2),true);
    ksort($file2);
    var_dump($file2);
    $difference = '';

    array_map(function ($key, $value) use (&$file2, &$difference) {
        if (array_key_exists($key, $file2)) {
            if ($value !== $file2[$key]) {
                $difference .= '- ' . $key . ': ' . (string)$value . "\n";
                $difference .= '+ ' . $key . ': ' . (string)$file2[$key] . "\n";
                $file2[$key] = null;
            } else {
                $difference .= $key . ': ' . (string)$value . "\n";
                $file2[$key] = null;
            }
        } else {
            $difference .= '- ' . $key . ': ' . (string)$value . "\n";
        }
    }, array_keys($file1), $file1);
    $newItems = array_filter($file2, fn($n) => !is_null($n));
    array_map(function ($key, $value) use (&$difference) {
            $difference .= '+ ' . $key . ': ' . (string)$value . "\n";
        }
    , array_keys($newItems), $newItems);
    var_dump($difference);
}