<?php

namespace Differ\Formatters\StylishFormat;

function getStylishFormated(array $buildDiff)
{
    if (is_multi($buildDiff)) {
        return "{\n" . iter($buildDiff) . "}";
    } else {
        return iter($buildDiff);
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

function iter(array $buildDiff, int $depth = 1)
{
    $result = "";
    array_walk($buildDiff, function ($item) use ($depth, &$result) {
        $key = $item['key'];
        $status = $item['status'];
        switch ($status) {
            case 'array':
                $children = $item['children'];
                $itemChildren = iter($children, $depth + 1);
                $result .= str_repeat(" ", $depth * 4) . $key . ": {\n"
                . $itemChildren . str_repeat(" ", $depth * 4) . "}\n";
                break;
            case 'no change':
                $value = $item['value'];
                $result .= str_repeat(" ", $depth * 4)
                . $key . ": " . getString($value) . "\n";
                break;
            case 'update':
                $oldValue = $item["oldValue"];
                $newValue = $item["newValue"];
                $result .= str_repeat(" ", $depth * 4 - 2)
                . "- " . $key . ": " . getString($oldValue) . "\n";
                $result .= str_repeat(" ", $depth * 4 - 2)
                . "+ " . $key . ": " . getString($newValue) . "\n";
                break;
            case 'add':
                $value = $item['value'];
                $result .= str_repeat(" ", $depth * 4 - 2)
                . "+ " . $key . ": " . getString($value) . "\n";
                break;
            case 'delete':
                $value = $item['value'];
                $result .= str_repeat(" ", $depth * 4 - 2)
                . "- " . $key . ": " . getString($value) . "\n";
                break;
            case 'add array':
                $children = $item['children'];
                $itemChildren = getStylishArray($children, $depth + 1);
                $result .= str_repeat(" ", $depth * 4 - 2)
                . "+ " . $key . ": {\n" . $itemChildren . str_repeat(" ", $depth * 4) . "}\n";
                break;
            case 'delete array':
                $children = $item['children'];
                $itemChildren = getStylishArray($children, $depth + 1);
                $result .= str_repeat(" ", $depth * 4 - 2)
                . "- " . $key . ": {\n" . $itemChildren . str_repeat(" ", $depth * 4) . "}\n";
                break;
            case 'update array':
                if (is_array($item["oldValue"])) {
                    $oldValue = getStylishArray($item["oldValue"], $depth + 1);
                    $result .= str_repeat(" ", $depth * 4 - 2)
                    . "- " . $key . ": {\n" . $oldValue . str_repeat(" ", $depth * 4) . "}\n";
                } else {
                    $oldValue = $item["oldValue"];
                    $result .= str_repeat(" ", $depth * 4 - 2)
                    . "- " . $key . ": " . getString($oldValue) . "\n";
                }
                if (is_array($item["newValue"])) {
                    $newValue = getStylishArray($item["newValue"], $depth + 1);
                    $result .= str_repeat(" ", $depth * 4 - 2)
                    . "+ " . $key . ": {\n" . $newValue . str_repeat(" ", $depth * 4) . "}\n";
                } else {
                    $newValue = $item["newValue"];
                    $result .= str_repeat(" ", $depth * 4 - 2)
                    . "+ " . $key . ": " . getString($newValue) . "\n";
                }
                break;
        }
    });
    return $result;
}

function getStylishArray($array, $depth = 1)
{
    $result = "";
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
            $itemChildren = getStylishArray($children, $depth + 1);
            $result .= str_repeat(" ", $depth * 4) . $key . ": {\n"
                . $itemChildren . str_repeat(" ", $depth * 4) . "}\n";
        } else {
            $value = $item['value'];
            $result .= str_repeat(" ", $depth * 4) . $key . ": " . getString($value) . "\n";
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
