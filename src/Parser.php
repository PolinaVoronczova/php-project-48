<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse(string $pathFile1, string $pathFile2)
{
    $file1 = file_get_contents($pathFile1);
    $file2 = file_get_contents($pathFile2);
    if ($file1 === false && $file2 === false) {
        return [0,0];
    } elseif ($file1 === false) {
        return [0,1];
    } elseif ($file2 === false) {
        return [1,0];
    }
    $pathInfoFile1 = pathinfo($pathFile1, PATHINFO_EXTENSION);
    $pathInfoFile2 = pathinfo($pathFile2, PATHINFO_EXTENSION);
    $isYml1 = $pathInfoFile1 === 'yml' || $pathInfoFile1 === 'yaml';
    $isYml2 = $pathInfoFile2 === 'yml' || $pathInfoFile2 === 'yaml';
    if (($pathInfoFile1 === 'json') && ($pathInfoFile2 === 'json')) {
        return [json_decode($file1, true), json_decode($file2, true)];
    } elseif ($isYml1 && $isYml2) {
        return [Yaml::parse($file1), Yaml::parse($file2)];
    }
}
