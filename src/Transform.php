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
class Transform implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface      $request
     * @param \Rougin\Slytherin\Middleware\HandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, HandlerInterface $handler)
    {
        $params = $request->getQueryParams();

        $get = $this->map($params);

        $request = $request->withQueryParams($get);

        $post = $request->getParsedBody();

        $parsed = is_array($post) ? $post : array();

        $post = $this->map($parsed);

        $post = $request->withParsedBody($post);

        return $handler->handle($post);
    }

    /**
     * Maps the array to transform each value.
     *
     * @param array<string, mixed> $items
     *
     * @return array<string, mixed>
     */
    protected function map($items)
    {
        foreach ($items as $key => $value)
        {
            $new = $this->transform($value);

            if (is_array($value))
            {
                $new = $this->map($value);
            }

            $items[$key] = $new;
        }

        return $items;
    }

    /**
     * Transforms the specified value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function transform($value)
    {
        return $value;
    }
}
