<?php 
namespace Differ\Formater;
use function Differ\Formatters\StylishFormat\getStylishFormated;
use function Differ\Formatters\PlainFormat\getPlainFormated;
use function Differ\Formatters\JsonFormat\getJsonFormated;
function getFormated($buildDiff, $format)
{
    switch ($format) {
    case 'stylish':
        return getStylishFormated($buildDiff);
    break;
    case 'plain':
        return getPlainFormated($buildDiff);
    break;
    case 'json':
        return getJsonFormated($buildDiff);
    break;
    default:
        return getStylishFormated($buildDiff);
    }
}


