<?php

namespace Differ\PlainFormat;

use function Functional\flatten;

function getPlainFormated(array $buildDiff, array $path = [])
{
    $resultArray = iter($buildDiff);
    return implode("\n", array_filter(flatten($resultArray)));
}

function iter(array $buildDiff, array $path = [])
{
    $result = array_reduce(
        $buildDiff,
        function ($result, $item) use ($path) {
            $key = $item['key'];
            $pathAdd = $key;
            $status = $item['status'];
            switch ($status) {
                case 'array':
                    $children = $item['children'];
                    $resultAdd = iter($children, array_merge($path, [$pathAdd]));
                    return array_merge($result, [$resultAdd]);
                case 'update':
                    $oldValue = $item["oldValue"];
                    $newValue = $item["newValue"];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                    . "' was updated. From " . getString($oldValue) . " to " . getString($newValue);
                    return array_merge($result, [$resultAdd]);
                case 'add':
                    $value = $item['value'];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                    . "' was added with value: " . getString($value);
                    return array_merge($result, [$resultAdd]);
                case 'delete':
                    $value = $item['value'];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd])) . "' was removed";
                    return array_merge($result, [$resultAdd]);
                case 'add array':
                    $children = $item['children'];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                    . "' was added with value: [complex value]";
                    return array_merge($result, [$resultAdd]);
                case 'delete array':
                    $children = $item['children'];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd])) . "' was removed";
                    return array_merge($result, [$resultAdd]);
                case 'update array':
                    if (is_array($item["oldValue"])) {
                        $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                        . "' was updated. From [complex value] to " . getString($item["newValue"]);
                        return array_merge($result, [$resultAdd]);
                    } elseif (is_array($item["newValue"])) {
                        $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                        . "' was updated. From " . getString($item["oldValue"]) . " to [complex value]";
                        return array_merge($result, [$resultAdd]);
                    }
                    break;
                default:
                    $resultAdd = '';
                    return array_merge($result, [$resultAdd]);
            }
        },
        []
    );
    return $result;
}

function getString(mixed $value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } elseif (is_null($value)) {
        return 'null';
    } elseif (is_string($value)) {
        return "'" . $value . "'";
    } elseif (is_int($value)) {
        return "{$value}";
    }
}
