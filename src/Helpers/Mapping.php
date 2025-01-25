<?php

namespace App\Helpers;

trait Mapping
{
    private static function getString(array $data, ...$keys): string
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return (string)$data[$key];
            }
        }

        return '';
    }

    private static function getInt(array $data, ...$keys): int
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return (int)$data[$key];
            }
        }

        return 0;
    }


    private static function getIntOrNull(array $data, ...$keys): ?int
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return self::getInt($data, $key);
            }
        }

        return null;
    }

    private static function getFloat(array $data, ...$keys): float
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return (float)$data[$key];
            }
        }

        return 0;
    }

    private static function getFloatOrNull(array $data, ...$keys): ?float
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return self::getFloat($data, $key);
            }
        }

        return null;
    }

    private static function getNonEmptyStringOrNull(
        array $data,
              ...$keys
    ): ?string {
        foreach ($keys as $key) {
            if (isset($data[$key]) && $data[$key] !== '') {
                return (string)$data[$key];
            }
        }

        return null;
    }

    private static function getArray(array $data, ...$keys): array
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return is_array($data[$key])
                    ? $data[$key]
                    : [$data[$key]];
            }
        }

        return [];
    }

    private static function getArrayOrNull(array $data, ...$keys): ?array
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return self::getArray($data, $key);
            }
        }

        return null;
    }

    private static function getBool(array $data, ...$keys): bool
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return (bool)$data[$key];
            }
        }

        return false;
    }
}
