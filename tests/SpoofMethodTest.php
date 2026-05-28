<?php

namespace Rougin\Onion;

use Rougin\Onion\Fixture\HandlerStub;
use Rougin\Slytherin\Http\Response;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class SpoofMethodTest extends Testcase
{
    /**
     * @var \Rougin\Onion\SpoofMethod
     */
    protected $self;

    /**
     * @return void
     */
    public function test_passed_if_custom_key()
    {
        $data = array('http_method' => 'PUT');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $self = new SpoofMethod('http_method');

        $self->process($request, $handler);

        $this->assertEquals('PUT', $handler->request()->getMethod());
    }

    /**
     * @return void
     */
    public function test_passed_if_key_setter_works()
    {
        $data = array('custom' => 'DELETE');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $self = new SpoofMethod;

        $self->key('custom');

        $self->process($request, $handler);

        $this->assertEquals('DELETE', $handler->request()->getMethod());
    }

    /**
     * @return void
     */
    public function test_passed_if_method_spoofed()
    {
        $data = array('_method' => 'PUT');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        $this->assertEquals('PUT', $handler->request()->getMethod());
    }

    /**
     * @return void
     */
    public function test_passed_if_method_uppercased()
    {
        $data = array('_method' => 'put');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        $this->assertEquals('PUT', $handler->request()->getMethod());
    }

    /**
     * @return void
     */
    public function test_passed_if_no_spoof_key()
    {
        $data = array('name' => 'John');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        $this->assertEquals('POST', $handler->request()->getMethod());

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('John', $parsed['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_non_string_method_skipped()
    {
        $data = array('_method' => 123);

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        $this->assertEquals('POST', $handler->request()->getMethod());
    }

    /**
     * @return void
     */
    public function test_passed_if_null_body()
    {
        $request = $this->createRequest('GET', array(), null);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        $this->assertEquals('GET', $handler->request()->getMethod());
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $this->self = new SpoofMethod;
    }
}
