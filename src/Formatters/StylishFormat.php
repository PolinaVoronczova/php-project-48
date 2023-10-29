<?php

namespace Differ\Formatters\StylishFormat;

use function Functional\flatten;

function getStylishFormated(array $buildDiff)
{
    $result = flatten(iter($buildDiff));
    return "{\n" . implode("\n", $result) . "\n" . "}";
}

function iter(array $buildDiff, int $depth = 1)
{
    $result = array_reduce(
        $buildDiff,
        function ($result, $item) use ($depth) {
            $key = $item['key'];
            $status = $item['status'];
            switch ($status) {
                case 'array':
                    $children = $item['children'];
                    $itemChildren = implode("\n", iter($children, $depth + 1));
                    $resultAdd = str_repeat(" ", $depth * 4) . $key . ": {\n"
                    . $itemChildren . "\n" . str_repeat(" ", $depth * 4) . "}";
                    return array_merge($result, [$resultAdd]);
                    break;
                case 'no change':
                    $value = $item['value'];
                    $resultAdd = str_repeat(" ", $depth * 4)
                    . $key . ": " . getString($value);
                    return array_merge($result, [$resultAdd]);
                    break;
                case 'update':
                    $oldValue = $item["oldValue"];
                    $newValue = $item["newValue"];
                    $resultAdd = str_repeat(" ", $depth * 4 - 2)
                    . "- " . $key . ": " . getString($oldValue) . "\n"
                    . str_repeat(" ", $depth * 4 - 2)
                    . "+ " . $key . ": " . getString($newValue);
                    return array_merge($result, [$resultAdd]);
                    break;
                case 'add':
                    $value = $item['value'];
                    $resultAdd = str_repeat(" ", $depth * 4 - 2)
                    . "+ " . $key . ": " . getString($value);
                    return array_merge($result, [$resultAdd]);
                    break;
                case 'delete':
                    $value = $item['value'];
                    $resultAdd = str_repeat(" ", $depth * 4 - 2)
                    . "- " . $key . ": " . getString($value);
                    return array_merge($result, [$resultAdd]);
                    break;
                case 'add array':
                    $children = $item['children'];
                    $itemChildren = implode("\n", getStylishArray($children, $depth + 1));
                    $resultAdd = str_repeat(" ", $depth * 4 - 2)
                    . "+ " . $key . ": {\n" . $itemChildren . "\n" . str_repeat(" ", $depth * 4) . "}";
                    return array_merge($result, [$resultAdd]);
                    break;
                case 'delete array':
                    $children = $item['children'];
                    $itemChildren = implode("\n", getStylishArray($children, $depth + 1));
                    $resultAdd = str_repeat(" ", $depth * 4 - 2)
                    . "- " . $key . ": {\n" . $itemChildren . "\n" . str_repeat(" ", $depth * 4) . "}";
                    return array_merge($result, [$resultAdd]);
                    break;
                case 'update array':
                    $updateResult = [];
                    if (is_array($item["oldValue"])) {
                        $oldValue = implode("\n", getStylishArray($item["oldValue"], $depth + 1));
                        $updateResult[] = str_repeat(" ", $depth * 4 - 2)
                        . "- " . $key . ": {\n" . $oldValue . "\n" . str_repeat(" ", $depth * 4) . "}";
                    } else {
                        $oldValue = $item["oldValue"];
                        $updateResult[] = str_repeat(" ", $depth * 4 - 2)
                        . "- " . $key . ": " . getString($oldValue);
                    }
                    if (is_array($item["newValue"])) {
                        $newValue = implode("\n", getStylishArray($item["newValue"], $depth + 1));
                        $updateResult[] = str_repeat(" ", $depth * 4 - 2)
                        . "+ " . $key . ": {\n" . $newValue . "\n" . str_repeat(" ", $depth * 4) . "}";
                    } else {
                        $newValue = $item["newValue"];
                        $updateResult[] = str_repeat(" ", $depth * 4 - 2)
                        . "+ " . $key . ": " . getString($newValue);
                    }
                    return array_merge($result, $updateResult);
                    break;
            }
        },
        []
    );
    return $result;
}

function getStylishArray(array $array, int $depth = 1, array $result = [])
{
    array_walk($array, function ($item) use ($depth, &$result) {
        $key = $item['key'];
        $status = $item['status'];
        $children = [];
        if (str_contains($status, 'array')) {
            if (isset($item['children'])) {
                $children = $item['children'];
            } elseif (isset($item['oldValue'])) {
                $children = $item['oldValue'];
            } elseif (isset($item['newValue'])) {
                $children = $item['newValue'];
            }
            $itemChildren = implode("\n", getStylishArray($children, $depth + 1));
            $result[] = str_repeat(" ", $depth * 4) . $key . ": {\n"
                . $itemChildren . "\n" . str_repeat(" ", $depth * 4) . "}";
        } else {
            $value = $item['value'];
            $result[] = str_repeat(" ", $depth * 4) . $key . ": " . getString($value);
        }
    });
    return $result;
}

function getString(mixed $value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } elseif (is_null($value)) {
        return 'null';
    } elseif (is_string($value)) {
        return $value;
    } elseif (is_int($value)) {
        return "{$value}";
    }
}
