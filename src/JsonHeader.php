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
class JsonHeader implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface      $request
     * @param \Rougin\Slytherin\Middleware\HandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, HandlerInterface $handler)
    {
        $response = $handler->handle($request);

        if (! $response->hasHeader('Content-Type'))
        {
            $response = $response->withHeader('Content-Type', 'application/json');
        }

        return $response;
    }
}
