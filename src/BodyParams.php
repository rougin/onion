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
        $endOfFirstLine = (int) strpos($input, "\r\n");

        $boundary = substr($input, 0, $endOfFirstLine);

        // Split form-data into each entry ---
        /** @var string[] */
        $parts = explode($boundary, $input);
        // -----------------------------------

        /** @var array<string, string> */
        $return = array();

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

            /** @var string[] */
            $headerParts = preg_split('#; |\r\n#', $head);

            $key = '';

            $thisHeader = array();

            // Parse the mini headers, obtain the key ---------------------
            foreach ($headerParts as $headerPart)
            {
                if (! preg_match('#(.*)(=|: )(.*)#', $headerPart, $keyVal))
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
            // ------------------------------------------------------------

            if ($key === '')
            {
                continue;
            }

            // If the key is multidimensional, generate --------
            // multidimentional array based off of the parts ---
            /** @var string[] */
            $nameParts = preg_split('#(?=\[.*\])#', $key);
            // -------------------------------------------------

            if (count($nameParts) < 1)
            {
                $return[$key] = $body;

                if (! isset($thisHeader['filename']))
                {
                    $header[$key] = $thisHeader;

                    continue;
                }

                /** @var string */
                $filename = tempnam(sys_get_temp_dir(), 'php');

                file_put_contents($filename, $body);

                $item = array('error' => 0);

                $item['name'] = $thisHeader['filename'];

                $item['type'] = $thisHeader['Content-Type'];

                $item['tmp_name'] = $filename;

                $item['size'] = filesize($body);

                $return[$key] = $item;

                $header[$key] = $thisHeader;

                continue;
            }

            $current = &$return;

            $currentHeader = &$header;

            $l = count($nameParts);

            for ($i = 0; $i < $l; $i++)
            {
                // Strip the array access tokens ------------------------
                /** @var string */
                $namePart = preg_replace('#[\[\]]#', '', $nameParts[$i]);
                // ------------------------------------------------------

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

                        if (is_array($current))
                        {
                            $current[$namePart] = array();
                        }
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

                    /** @var string */
                    $filename = tempnam($temp, 'php');

                    file_put_contents($filename, $body);

                    $file = array('error' => 0);

                    $file['name'] = $thisHeader['filename'];

                    $file['type'] = $thisHeader['Content-Type'];

                    $file['tmp_name'] = $filename;

                    $file['size'] = filesize($body);

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

        return $return;
    }
}
