<?php

namespace Rougin\Onion;

/**
 * Overrides the global function to allow mocking php://input in tests.
 *
 * @param string $filename
 *
 * @return string
 */
function file_get_contents($filename)
{
    $isInput = $filename === 'php://input';

    $exists = array_key_exists('_php_input', $GLOBALS);

    if ($isInput && $exists)
    {
        /** @var string */
        $result = $GLOBALS['_php_input'];

        return $result;
    }

    /** @var string */
    $result = file_get_contents($filename);

    return $result;
}

require __DIR__ . '/../vendor/autoload.php';
