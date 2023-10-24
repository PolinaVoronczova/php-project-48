<?php

namespace Differ\Formatters\JsonFormat;

function getJsonFormated(array $buildDiff)
{
    return json_encode(iter($buildDiff), JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);
}

function iter(array $buildDiff, array $result = [])
{
    foreach ($buildDiff as $item) {
        $key = $item['key'];
        $status = $item['status'];
        switch ($status) {
            case 'array':
                $children = $item['children'];
                $result[] = ['key' => $key, 'status' => 'array', 'children' => iter($children)];
                break;
            case 'no change':
                $value = $item['value'];
                $result[] = ['key' => $key, 'status' => 'no change', 'value' => $value];
                break;
            case 'update':
                $oldValue = $item["oldValue"];
                $newValue = $item["newValue"];
                $result[] = ['key' => $key, 'status' => 'was updated',
                'old value' => $oldValue, 'new value' => $newValue];
                break;
            case 'add':
                $value = $item['value'];
                $result[] = ['key' => $key,
                'status' => 'was added', 'value' => $value];
                break;
            case 'delete':
                $value = $item['value'];
                $result[] = ['key' => $key,
                'status' => 'was removed', 'removed value' => $value];
                break;
            case 'add array':
                $children = $item['children'];
                $result[] = ['key' => $key,
                'status' => 'was added array', 'children' => iter($children)];
                break;
            case 'delete array':
                $children = $item['children'];
                $result[] = ['key' => $key,
                'status' => 'was removed array', 'children' => iter($children)];
                break;
            case 'update array':
                $oldValue = $item["oldValue"];
                $newValue = $item["newValue"];
                if (is_array($oldValue)) {
                    $result[] = ['key' => $key, 'status' => 'was updated array',
                    'old value' => iter($oldValue), 'new value' => $newValue];
                } elseif (is_array($newValue)) {
                    $result[] = ['key' => $key, 'status' => 'was updated array',
                    'old value' => $oldValue, 'new value' => iter($newValue)];
                }
                break;
        }
    };
    return $result;
}
