<?php
namespace Differ\Formatters\PlainFormat;

function getPlainFormated(array $buildDiff, array $path = [])
{
    $result = "";
    array_walk($buildDiff, function ($item) use ($path, &$result) {
        $key = $item['key'];
        $path[] = $key;
        $status = $item['status'];
        switch ($status) {
            case 'array':
                $children = $item['children'];
                $result .= getPlainFormated($children, $path);
                break;
            case 'update':
                $oldValue = $item["oldValue"];
                $newValue = $item["newValue"];
                $result .= "Property '" . implode('.', $path) . "' was updated. From " . getString($oldValue) . " to " . getString($newValue) . "\n";
                break;
            case 'add':
                $value = $item['value'];
                $result .= "Property '" . implode('.', $path) . "' was added with value: " . getString($value) . "\n";
                break;
            case 'delete':
                $value = $item['value'];
                $result .= "Property '" . implode('.', $path) . "' was removed\n";
                break;
            case 'add array':
                $children = $item['children'];
                $itemChildren = getPlainFormated($children, $path);
                $result .= "Property '" . implode('.', $path) . "' was added with value: [complex value]\n";
                break;
            case 'delete array':
                $children = $item['children'];
                $itemChildren = getPlainFormated($children, $path);
                $result .= "Property '" . implode('.', $path) . "' was removed\n";
                break;
            case 'update array':
                if (is_array($item["oldValue"])) {
                    $result .= "Property '" . implode('.', $path) . "' was updated. From [complex value] to " . getString($item["newValue"]) . "\n";
                } elseif (is_array($item["newValue"])) {
                    $result .= "Property '" . implode('.', $path) . "' was updated. From " . getString($item["oldValue"]) . " to [complex value]\n";
                }
                break;
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
        return "'" . $value . "'";
    } elseif (is_int($value)) {
        return "{$value}";
    }
}