<?php

namespace Differ\Formater;

use function Differ\Formatters\StylishFormat\getStylishFormated;
use function Differ\Formatters\PlainFormat\getPlainFormated;
use function Differ\Formatters\JsonFormat\getJsonFormated;

function getFormated(array $buildDiff, string $format)
{
    switch ($format) {
        case 'stylish':
            return getStylishFormated($buildDiff);
        case 'plain':
            return getPlainFormated($buildDiff);
        case 'json':
            return getJsonFormated($buildDiff);
        default:
            return getStylishFormated($buildDiff);
    }
}
