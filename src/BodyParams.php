<?php

namespace Rougin\Onion;

use Psr\Http\Message\ServerRequestInterface;
use Rougin\Slytherin\Middleware\HandlerInterface;
use Rougin\Slytherin\Middleware\MiddlewareInterface;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class BodyParams implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    protected $complex = array('PATCH', 'PUT', 'DELETE');

    /**
     * @param \Psr\Http\Message\ServerRequestInterface      $request
     * @param \Rougin\Slytherin\Middleware\HandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, HandlerInterface $handler)
    {
        $method = $request->getMethod();

        if (! in_array($method, $this->complex))
        {
            return $handler->handle($request);
        }

        /** @var array<string, mixed> */
        $post = $request->getParsedBody();

        /** @var string */
        $contents = file_get_contents('php://input');

        /** @var array<string, string> */
        $parsed = array();

        parse_str($contents, $parsed);

        if (strpos($contents, 'form-data') !== false)
        {
            $parsed = $this->parse($contents);
        }

        $parsed = array_merge($post, $parsed);

        $request = $request->withParsedBody($parsed);

        return $handler->handle($request);
    }

    /**
     * https://stackoverflow.com/a/38624774
     *
     * @param string $input
     *
     * @return array<mixed, mixed>
     */
    protected function parse($input)
    {
        /** @var array<string, mixed> */
        $return = array();

        $endOfFirstLine = strpos($input, "\r\n");

        if ($endOfFirstLine < 1)
        {
            return $return;
        }

        /** @var non-empty-string */
        $boundary = substr($input, 0, $endOfFirstLine);

        // Split form-data into each entry ---
        /** @var non-empty-string[] */
        $parts = explode($boundary, $input);
        // -----------------------------------

        /** @var array<string, mixed> */
        $header = array();

        // Remove first and last (null) entries ---
        array_shift($parts);

        array_pop($parts);
        // ----------------------------------------

        foreach ($parts as $part)
        {
            $endOfHead = strpos($part, "\r\n\r\n");

            $startOfBody = $endOfHead + 4;

            $head = substr($part, 2, $endOfHead - 2);

            $body = substr($part, $startOfBody, -2);

            $result = preg_split('#; |\r\n#', $head);

            $headerParts = $result === false ? array() : $result;

            $key = '';

            $thisHeader = array();

            // Parse the mini headers, obtain the key ------------------
            $search = '#(.*)(=|: )(.*)#';

            foreach ($headerParts as $headerPart)
            {
                if (! preg_match($search, $headerPart, $keyVal))
                {
                    continue;
                }

                if ($keyVal[1] === 'name')
                {
                    $key = substr($keyVal[3], 1, -1);

                    continue;
                }

                if ($keyVal[2] === '=')
                {
                    $thisHeader[$keyVal[1]] = substr($keyVal[3], 1, -1);

                    continue;
                }

                $thisHeader[$keyVal[1]] = $keyVal[3];
            }
            // ---------------------------------------------------------

            if ($key === '')
            {
                continue;
            }

            // If the key is multidimensional, generate --------
            // multidimentional array based off of the parts ---
            $result = preg_split('#(?=\[.*\])#', $key);

            $nameParts = $result === false ? array() : $result;
            // -------------------------------------------------

            $current = &$return;

            $currentHeader = &$header;

            $l = count($nameParts);

            for ($i = 0; $i < $l; $i++)
            {
                // Strip the array access tokens ---------------------
                $search = '#[\[\]]#';

                /** @var string */
                $namePart = preg_replace($search, '', $nameParts[$i]);
                // ---------------------------------------------------

                // Add data to array if at the end of the depth of this entry ---
                if ($i != $l - 1)
                {
                    // Advance into the array -----------------------------
                    if (is_array($current) && ! isset($current[$namePart]))
                    {
                        if (is_array($currentHeader))
                        {
                            $currentHeader[$namePart] = array();
                        }

                        $current[$namePart] = array();
                    }
                    // ----------------------------------------------------

                    if (is_array($currentHeader))
                    {
                        $currentHeader = &$currentHeader[$namePart];
                    }

                    if (is_array($current))
                    {
                        $current = &$current[$namePart];
                    }

                    continue;
                }
                // --------------------------------------------------------------

                if (is_array($current))
                {
                    $current[$namePart] = $body;
                }

                if (isset($thisHeader['filename']))
                {
                    $temp = sys_get_temp_dir();

                    /** @var non-falsy-string */
                    $filename = tempnam($temp, 'php');

                    file_put_contents($filename, $body);

                    $file = array('error' => 0);

                    $file['name'] = $thisHeader['filename'];

                    $file['type'] = $thisHeader['Content-Type'];

                    $file['tmp_name'] = $filename;

                    $file['size'] = strlen($body);

                    if (is_array($current))
                    {
                        $current[$namePart] = $file;
                    }
                }

                if (is_array($currentHeader))
                {
                    $currentHeader[$namePart] = $thisHeader;
                }
            }
        }

        return is_array($return) ? $return : array();
    }
}
