<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse(string $pathFile)
{
    $file = file_get_contents($pathFile);
    if ($file === false) {
        throw new \Exception("File {$pathFile} no exist.");
    }
    $pathInfoFile = pathinfo($pathFile, PATHINFO_EXTENSION);
    $isYml = $pathInfoFile === 'yml';
    if (($pathInfoFile === 'json')) {
        return json_decode($file, true);
    } elseif ($isYml) {
        return Yaml::parse($file);
    }
}
