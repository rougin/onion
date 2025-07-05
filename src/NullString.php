<?php

namespace Rougin\Onion;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class NullString extends Transform
{
    /**
     * @var string[]
     */
    protected $values = array('null', '', 'undefined');

    /**
     * Transforms the specified value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function transform($value)
    {
        return is_string($value) && in_array($value, $this->values) ? null : $value;
    }
}
