<?php
declare(strict_types=1);


class ArrayMerger
{
    public function mergeUnique(array ...$arrays): array
    {
        $seen = [];
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $value) {
                $key = serialize($value);
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}