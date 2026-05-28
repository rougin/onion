<?php

namespace Rougin\Onion;

use Psr\Http\Message\ServerRequestInterface;
use Rougin\Slytherin\Http\Response;
use Rougin\Slytherin\Middleware\HandlerInterface;
use Rougin\Slytherin\Middleware\MiddlewareInterface;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CorsHeader implements MiddlewareInterface
{
    const METHODS = 'Access-Control-Allow-Methods';

    const ORIGIN = 'Access-Control-Allow-Origin';

    /**
     * @var string[]
     */
    protected $allowed = array();

    /**
     * @var string[]
     */
    protected $methods = array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS');

    /**
     * Initializes the middleware instance.
     *
     * @param string[] $allowed
     * @param string[] $methods
     */
    public function __construct($allowed = array(), $methods = array())
    {
        $this->allowed(empty($allowed) ? array('*') : $allowed);

        $this->methods(empty($methods) ? $this->methods : $methods);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface      $request
     * @param \Rougin\Slytherin\Middleware\HandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, HandlerInterface $handler)
    {
        if ($request->getMethod() === 'OPTIONS')
        {
            $response = new Response;
        }
        else
        {
            $response = $handler->handle($request);
        }

        $response = $response->withHeader(self::ORIGIN, $this->allowed);

        return $response->withHeader(self::METHODS, $this->methods);
    }

    /**
     * Sets the allowed URLS.
     *
     * @param string[] $allowed
     *
     * @return self
     */
    public function allowed($allowed)
    {
        $this->allowed = $allowed;

        return $this;
    }

    /**
     * Sets the allowed HTTP methods.
     *
     * @param string[] $methods
     *
     * @return self
     */
    public function methods($methods)
    {
        $this->methods = $methods;

        return $this;
    }
}
