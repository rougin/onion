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
class SpoofMethod implements MiddlewareInterface
{
    /**
     * @var string
     */
    protected $key = '';

    /**
     * Initializes the middleware instance.
     *
     * @param string $key
     */
    public function __construct($key = '_method')
    {
        $this->key($key);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface      $request
     * @param \Rougin\Slytherin\Middleware\HandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, HandlerInterface $handler)
    {
        /** @var array<string, mixed>|object|null */
        $parsed = $request->getParsedBody();

        /** @var array<string, mixed> */
        $parsed = is_array($parsed) ? $parsed : array();

        if (array_key_exists($this->key, $parsed) && is_string($parsed[$this->key]))
        {
            $method = strtoupper($parsed[$this->key]);

            $request = $request->withMethod($method);
        }

        return $handler->handle($request);
    }

    /**
     * Sets the key for the HTTP method.
     *
     * @param string $key
     *
     * @return self
     */
    public function key($key)
    {
        $this->key = $key;

        return $this;
    }
}
