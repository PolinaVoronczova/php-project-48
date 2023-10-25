<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parser(string $pathFile1, string $pathFile2)
{
    $file1 = file_get_contents($pathFile1);
    $file2 = file_get_contents($pathFile2);
    if (!($file1 && $file2)) {
        return [0,0];
    } elseif (!($file1)) {
        return [0,1];
    } elseif (!($file2)) {
        return [1,0];
    }
    $pathInfoFile1 = pathinfo($pathFile1, PATHINFO_EXTENSION);
    $pathInfoFile2 = pathinfo($pathFile2, PATHINFO_EXTENSION);
    $isYml1 = $pathInfoFile1 === 'yml' || $pathInfoFile1 === 'yaml';
    $isYml2 = $pathInfoFile2 === 'yml' || $pathInfoFile2 === 'yaml';
    $array1 = [];
    $array2 = [];
    if (($pathInfoFile1 === 'json') && ($pathInfoFile2 === 'json')) {
        $array1 = json_decode($file1, true);
        $array2 = json_decode($file2, true);
    } elseif ($isYml1 && $isYml2) {
        $array1 = Yaml::parse($file1);
        $array2 = Yaml::parse($file2);
    }
    return [$array1, $array2];
}
