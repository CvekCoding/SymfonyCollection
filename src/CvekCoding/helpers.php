<?php

use Symfony\Component\PropertyAccess\PropertyAccess;
use Tightenco\Collect\Support\Arr;
use Tightenco\Collect\Support\Collection;

/**
 * Get an item from an array or object using "dot" notation.
 *
 * @param mixed        $target
 * @param string|array $key
 * @param mixed        $default
 *
 * @return mixed
 */
function symfony_data_get($target, $key, $default = null)
{
    if (null === $key) {
        return $target;
    }

    $propertyAccessor = PropertyAccess::createPropertyAccessor();
    $key = is_array($key) ? $key : explode('.', $key);

    while (($segment = array_shift($key)) !== null) {
        if ($segment === '*') {
            if ($target instanceof Collection) {
                $target = $target->all();
            } elseif (!is_array($target)) {
                return value($default);
            }

            $result = Arr::pluck($target, $key);

            return in_array('*', $key, true) ? Arr::collapse($result) : $result;
        }

        if (Arr::accessible($target) && Arr::exists($target, $segment)) {
            $target = $target[$segment];
        } elseif (is_object($target)) {
            $target = $propertyAccessor->getValue($target, $segment);
        } else {
            return value($default);
        }
    }

    return $target;
}
