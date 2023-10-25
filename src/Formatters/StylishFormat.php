<?php

namespace Differ\Formatters\StylishFormat;

use function Functional\flatten;

function getStylishFormated(array $buildDiff)
{
    if (is_multi($buildDiff)) {
        $result = flatten(iter($buildDiff));
        return "{\n" . implode("\n", $result) . "\n" . "}";
    } else {
        return implode("\n", iter($buildDiff)) . "\n";
    }
}

function is_multi($array)
{
    foreach ($array as $key => $item) {
        if ($key == 'children') {
            return true;
        } elseif (is_array($item) && is_multi($item)) {
            return true;
        }
    }
    return false;
}

function iter(array $buildDiff, int $depth = 1, array $result = [])
{
    array_walk($buildDiff, function ($item) use ($depth, &$result) {
        $key = $item['key'];
        $status = $item['status'];
        switch ($status) {
            case 'array':
                $children = $item['children'];
                $itemChildren = implode("\n", iter($children, $depth + 1));
                $result[] = str_repeat(" ", $depth * 4) . $key . ": {\n"
                . $itemChildren . "\n" . str_repeat(" ", $depth * 4) . "}";
                break;
            case 'no change':
                $value = $item['value'];
                $result[] = str_repeat(" ", $depth * 4)
                . $key . ": " . getString($value);
                break;
            case 'update':
                $oldValue = $item["oldValue"];
                $newValue = $item["newValue"];
                $result[] = str_repeat(" ", $depth * 4 - 2)
                . "- " . $key . ": " . getString($oldValue);
                $result[] = str_repeat(" ", $depth * 4 - 2)
                . "+ " . $key . ": " . getString($newValue);
                break;
            case 'add':
                $value = $item['value'];
                $result[] = str_repeat(" ", $depth * 4 - 2)
                . "+ " . $key . ": " . getString($value);
                break;
            case 'delete':
                $value = $item['value'];
                $result[] = str_repeat(" ", $depth * 4 - 2)
                . "- " . $key . ": " . getString($value);
                break;
            case 'add array':
                $children = $item['children'];
                $itemChildren = implode("\n", getStylishArray($children, $depth + 1));
                $result[] = str_repeat(" ", $depth * 4 - 2)
                . "+ " . $key . ": {\n" . $itemChildren . "\n" . str_repeat(" ", $depth * 4) . "}";
                break;
            case 'delete array':
                $children = $item['children'];
                $itemChildren = implode("\n", getStylishArray($children, $depth + 1));
                $result[] = str_repeat(" ", $depth * 4 - 2)
                . "- " . $key . ": {\n" . $itemChildren . "\n" . str_repeat(" ", $depth * 4) . "}";
                break;
            case 'update array':
                if (is_array($item["oldValue"])) {
                    $oldValue = implode("\n", getStylishArray($item["oldValue"], $depth + 1));
                    $result[] = str_repeat(" ", $depth * 4 - 2)
                    . "- " . $key . ": {\n" . $oldValue . "\n" . str_repeat(" ", $depth * 4) . "}";
                } else {
                    $oldValue = $item["oldValue"];
                    $result[] = str_repeat(" ", $depth * 4 - 2)
                    . "- " . $key . ": " . getString($oldValue);
                }
                if (is_array($item["newValue"])) {
                    $newValue = implode("\n", getStylishArray($item["newValue"], $depth + 1));
                    $result[] = str_repeat(" ", $depth * 4 - 2)
                    . "+ " . $key . ": {\n" . $newValue . "\n" . str_repeat(" ", $depth * 4) . "}";
                } else {
                    $newValue = $item["newValue"];
                    $result[] = str_repeat(" ", $depth * 4 - 2)
                    . "+ " . $key . ": " . getString($newValue);
                }
                break;
        }
    });
    return $result;
}

function getStylishArray($array, $depth = 1, array $result = [])
{
    array_walk($array, function ($item) use ($depth, &$result) {
        $key = $item['key'];
        $status = $item['status'];
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

function getString($value)
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
