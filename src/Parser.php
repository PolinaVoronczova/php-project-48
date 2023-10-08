<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parser($pathFile1, $pathFile2)
{
    $file1 = file_get_contents($pathFile1);
    $file2 = file_get_contents($pathFile2);
    $pathInfoFile1 = pathinfo($pathFile1, PATHINFO_EXTENSION);
    $pathInfoFile2 = pathinfo($pathFile2, PATHINFO_EXTENSION);
    if (($pathInfoFile1 === 'json') && ($pathInfoFile2 === 'json')) {
        $array1 = json_decode($file1, true);
        $array2 = json_decode($file2, true);
    } elseif (($pathInfoFile1 === 'yml' || $pathInfoFile1 === 'yaml') && ($pathInfoFile2 === 'yml' || $pathInfoFile2 === 'yaml')) {
        $array1 = Yaml::parse($file1);
        $array2 = Yaml::parse($file2);
    }
    return [$array1, $array2];
}