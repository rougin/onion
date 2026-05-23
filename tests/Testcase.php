<?php

namespace Rougin\Onion;

use LegacyPHPUnit\TestCase as Legacy;
use Rougin\Slytherin\Http\ServerRequest;

/**
 * @codeCoverageIgnore
 *
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Testcase extends Legacy
{
    /**
     * Creates a new server request instance.
     *
     * @param string                    $method
     * @param array<string, string>     $query
     * @param array<string, mixed>|null $data
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function createRequest($method, array $query = array(), $data = array())
    {
        /** @var array<string, string> */
        $server = array('REQUEST_METHOD' => $method, 'REQUEST_URI' => '/');

        $server['SERVER_NAME'] = 'localhost';

        $server['SERVER_PORT'] = '80';

        return new ServerRequest($server, array(), $query, array(), $data);
    }

    /**
     * @param class-string $exception
     *
     * @return void
     */
    protected function doExpectException($exception)
    {
        /** @phpstan-ignore-next-line */
        if (method_exists($this, 'expectException'))
        {
            /** @phpstan-ignore-next-line */
            $this->expectException($exception);

            return;
        }

        /** @phpstan-ignore-next-line */
        $this->setExpectedException($exception);
    }
}
