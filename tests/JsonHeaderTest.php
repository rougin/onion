<?php

namespace Rougin\Onion;

use Rougin\Onion\Fixture\HandlerStub;
use Rougin\Slytherin\Http\Response;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class JsonHeaderTest extends Testcase
{
    /**
     * @var \Rougin\Onion\JsonHeader
     */
    protected $self;

    /**
     * @return void
     */
    public function test_passed_if_content_type_already_set()
    {
        $data = array('Content-Type' => array('text/html'));

        $response = new Response(200, null, $data);

        $handler = new HandlerStub($response);

        $request = $this->createRequest('GET');

        $result = $this->self->process($request, $handler);

        $this->assertEquals('text/html', $result->getHeaderLine('Content-Type'));
    }

    /**
     * @return void
     */
    public function test_passed_if_content_type_not_set()
    {
        $handler = new HandlerStub(new Response);

        $request = $this->createRequest('GET');

        $response = $this->self->process($request, $handler);

        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }

    /**
     * @return void
     */
    public function test_passed_if_custom_headers_kept()
    {
        $data = array('X-Custom' => array('foo'));

        $response = new Response(200, null, $data);

        $handler = new HandlerStub($response);

        $request = $this->createRequest('GET');

        $result = $this->self->process($request, $handler);

        $this->assertEquals('application/json', $result->getHeaderLine('Content-Type'));

        $this->assertEquals('foo', $result->getHeaderLine('X-Custom'));
    }

    /**
     * @return void
     */
    public function test_passed_if_status_code_preserved()
    {
        $handler = new HandlerStub(new Response(201));

        $request = $this->createRequest('GET');

        $response = $this->self->process($request, $handler);

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $this->self = new JsonHeader;
    }
}
