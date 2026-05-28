<?php

namespace Rougin\Onion;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class TrimString extends Transform
{
    /**
     * Transforms the specified value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function transform($value)
    {
        return is_string($value) ? trim($value) : $value;
    }
}
