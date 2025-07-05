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
class FormParser implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface      $request
     * @param \Rougin\Slytherin\Middleware\HandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, HandlerInterface $handler)
    {
        /** @var string */
        $result = file_get_contents('php://input');

        /** @var array<string, mixed>|null */
        $data = json_decode($result, true);

        /** @var array<string, string> */
        $post = $request->getParsedBody();

        if (! $data)
        {
            parse_str($result, $data);
        }

        /** @var array<string, mixed> $data */
        foreach ($data as $key => $value)
        {
            $updated = str_replace('amp;', '', $key);

            unset($data[$key]);

            $data[$updated] = $value;
        }

        $post = array_merge($post, $data);

        $request->withParsedBody($post);

        return $handler->handle($request);
    }
}
