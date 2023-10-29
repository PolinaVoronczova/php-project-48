<?php

namespace Differ\Formatters\PlainFormat;

use function Functional\flatten;

function getPlainFormated(array $buildDiff, array $path = [])
{
    $resultArray = iter($buildDiff);
    return implode("\n", flatten($resultArray));
}

function iter(array $buildDiff, array $path = [])
{
    $result = array_reduce($buildDiff, function ($result, $item) use ($path) {
        $key = $item['key'];
        $path[] = $key;
        $status = $item['status'];
        switch ($status) {
            case 'array':
                $children = $item['children'];
                $result[] = iter($children, $path);
                break;
            case 'update':
                $oldValue = $item["oldValue"];
                $newValue = $item["newValue"];
                $result[] = "Property '" . implode('.', $path) . "' was updated. From "
                . getString($oldValue) . " to " . getString($newValue);
                break;
            case 'add':
                $value = $item['value'];
                $result[] = "Property '" . implode('.', $path) . "' was added with value: "
                . getString($value);
                break;
            case 'delete':
                $value = $item['value'];
                $result[] = "Property '" . implode('.', $path) . "' was removed";
                break;
            case 'add array':
                $children = $item['children'];
                $result[] = "Property '" . implode('.', $path)
                . "' was added with value: [complex value]";
                break;
            case 'delete array':
                $children = $item['children'];
                $result[] = "Property '" . implode('.', $path) . "' was removed";
                break;
            case 'update array':
                if (is_array($item["oldValue"])) {
                    $result[] = "Property '" . implode('.', $path)
                    . "' was updated. From [complex value] to " . getString($item["newValue"]);
                } elseif (is_array($item["newValue"])) {
                    $result[] = "Property '" . implode('.', $path)
                    . "' was updated. From " . getString($item["oldValue"]) . " to [complex value]";
                }
                break;
        }
        return $result;
    }, []);
    return $result;
}

function getString($value)
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
