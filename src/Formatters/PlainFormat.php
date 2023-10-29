<?php

namespace Differ\Formatters\PlainFormat;

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
                    break;
                case 'update':
                    $oldValue = $item["oldValue"];
                    $newValue = $item["newValue"];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                    . "' was updated. From " . getString($oldValue) . " to " . getString($newValue);
                    break;
                case 'add':
                    $value = $item['value'];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                    . "' was added with value: " . getString($value);
                    break;
                case 'delete':
                    $value = $item['value'];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd])) . "' was removed";
                    break;
                case 'add array':
                    $children = $item['children'];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                    . "' was added with value: [complex value]";
                    break;
                case 'delete array':
                    $children = $item['children'];
                    $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd])) . "' was removed";
                    break;
                case 'update array':
                    if (is_array($item["oldValue"])) {
                        $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                        . "' was updated. From [complex value] to " . getString($item["newValue"]);
                    } elseif (is_array($item["newValue"])) {
                        $resultAdd = "Property '" . implode('.', array_merge($path, [$pathAdd]))
                        . "' was updated. From " . getString($item["oldValue"]) . " to [complex value]";
                    }
                    break;
                default:
                    $resultAdd = '';
                    break;
            }
            return array_merge($result, [$resultAdd]);
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
