<?php

namespace Differ\JsonFormat;

function getJsonFormated(array $buildDiff)
{
    return json_encode($buildDiff, JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);
}
