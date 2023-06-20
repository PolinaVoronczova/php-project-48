<?php 
namespace Differ\Parser;

function parser($pathFile1, $pathFile2)
{
    $file1 = file_get_contents($pathFile1);
    $file2 = file_get_contents($pathFile2);
    if ((pathinfo($pathFile1, PATHINFO_EXTENSION) === 'json') && ((pathinfo($pathFile2, PATHINFO_EXTENSION) === 'json'))) {
        $array1 = json_decode($file1, true);
        $array2 = json_decode($file2, true);
    } elseif ((pathinfo($pathFile1, PATHINFO_EXTENSION) === 'yml' || pathinfo($pathFile1, PATHINFO_EXTENSION) === 'yaml') && (pathinfo($pathFile2, PATHINFO_EXTENSION) === 'yml' || pathinfo($pathFile2, PATHINFO_EXTENSION) === 'yaml'))
    {
        $array1 = Yaml::parse($file1);
        $array2 = Yaml::parse($file2);
    }
    return [$array1, $array2];
}