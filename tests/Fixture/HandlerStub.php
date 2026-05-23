<?php

namespace Rougin\Onion\Fixture;

use Psr\Http\Message\ServerRequestInterface;
use Rougin\Slytherin\Http\Response;
use Rougin\Slytherin\Middleware\HandlerInterface;

/**
 * @codeCoverageIgnore
 *
 * @package Onion
 */
class HandlerStub implements HandlerInterface
{
    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * @param \Psr\Http\Message\ResponseInterface|null $response
     */
    public function __construct($response = null)
    {
        $this->response = $response === null ? new Response : $response;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request)
    {
        $this->request = $request;

        return $this->response;
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function request()
    {
        return $this->request;
    }
}
