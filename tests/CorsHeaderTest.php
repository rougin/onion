<?php

namespace Rougin\Onion;

use Rougin\Onion\Fixture\HandlerStub;
use Rougin\Slytherin\Http\Response;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CorsHeaderTest extends Testcase
{
    /**
     * @return void
     */
    public function test_passed_if_allowed_setter_works()
    {
        $middleware = new CorsHeader;

        $middleware->allowed(array('https://api.example.com'));

        $handler = new HandlerStub;

        $request = $this->createRequest('GET');

        $response = $middleware->process($request, $handler);

        $this->assertEquals('https://api.example.com', $response->getHeaderLine('Access-Control-Allow-Origin'));
    }

    /**
     * @return void
     */
    public function test_passed_if_custom_methods_set()
    {
        $middleware = new CorsHeader(null, array('GET', 'POST'));

        $handler = new HandlerStub;

        $request = $this->createRequest('GET');

        $response = $middleware->process($request, $handler);

        $this->assertEquals('GET,POST', $response->getHeaderLine('Access-Control-Allow-Methods'));
    }

    /**
     * @return void
     */
    public function test_passed_if_custom_origin_set()
    {
        $middleware = new CorsHeader(array('https://example.com'));

        $handler = new HandlerStub;

        $request = $this->createRequest('GET');

        $response = $middleware->process($request, $handler);

        $this->assertEquals('https://example.com', $response->getHeaderLine('Access-Control-Allow-Origin'));
    }

    /**
     * @return void
     */
    public function test_passed_if_default_methods_set()
    {
        $middleware = new CorsHeader;

        $handler = new HandlerStub;

        $request = $this->createRequest('GET');

        $response = $middleware->process($request, $handler);

        /** @var string */
        $header = $response->getHeaderLine('Access-Control-Allow-Methods');

        $this->assertContains('GET', explode(',', $header));

        $this->assertContains('POST', explode(',', $header));

        $this->assertContains('PUT', explode(',', $header));

        $this->assertContains('DELETE', explode(',', $header));

        $this->assertContains('OPTIONS', explode(',', $header));
    }

    /**
     * @return void
     */
    public function test_passed_if_default_origin_is_star()
    {
        $middleware = new CorsHeader;

        $handler = new HandlerStub;

        $request = $this->createRequest('GET');

        $response = $middleware->process($request, $handler);

        $this->assertEquals('*', $response->getHeaderLine('Access-Control-Allow-Origin'));
    }

    /**
     * @return void
     */
    public function test_passed_if_methods_setter_works()
    {
        $middleware = new CorsHeader;

        $middleware->methods(array('GET'));

        $handler = new HandlerStub;

        $request = $this->createRequest('GET');

        $response = $middleware->process($request, $handler);

        $this->assertEquals('GET', $response->getHeaderLine('Access-Control-Allow-Methods'));
    }

    /**
     * @return void
     */
    public function test_passed_if_non_options_passes_through()
    {
        $middleware = new CorsHeader;

        $handler = new HandlerStub(new Response(201));

        $request = $this->createRequest('POST');

        $response = $middleware->process($request, $handler);

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertEquals('*', $response->getHeaderLine('Access-Control-Allow-Origin'));
    }

    /**
     * @return void
     */
    public function test_passed_if_options_short_circuits()
    {
        $middleware = new CorsHeader;

        $handler = new HandlerStub(new Response(500));

        $request = $this->createRequest('OPTIONS');

        $response = $middleware->process($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('*', $response->getHeaderLine('Access-Control-Allow-Origin'));
    }
}
