<?php

namespace Rougin\Onion;

use Rougin\Onion\Fixture\HandlerStub;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class NullStringTest extends Testcase
{
    /**
     * @var \Rougin\Onion\NullString
     */
    protected $self;

    /**
     * @return void
     */
    public function test_passed_if_empty_string_parsed()
    {
        $data = array('name' => '');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertNull($parsed['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_integer_unchanged()
    {
        $data = array('age' => 42);

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals(42, $parsed['age']);
    }

    /**
     * @return void
     */
    public function test_passed_if_mixed_nested_values_kept()
    {
        $data = array('item' => array('a' => '', 'b' => 'hello', 'c' => 99));

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        /** @var array<string, mixed> $parsedItem */
        $parsedItem = $parsed['item'];

        $this->assertNull($parsedItem['a']);

        $this->assertEquals('hello', $parsedItem['b']);

        $this->assertEquals(99, $parsedItem['c']);
    }

    /**
     * @return void
     */
    public function test_passed_if_nested_value_converted()
    {
        $data = array('filter' => array('name' => ''));

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        /** @var array<string, mixed> $parsedFilter */
        $parsedFilter = $parsed['filter'];

        $this->assertNull($parsedFilter['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_normal_string_unchanged()
    {
        $data = array('name' => 'hello');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('hello', $parsed['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_null_string_parsed()
    {
        $data = array('name' => 'null');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertNull($parsed['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_query_and_body_converted()
    {
        $query = array('q' => 'null');

        $data = array('name' => '');

        $request = $this->createRequest('POST', $query, $data);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        $stubRequest = $handler->request();

        $params = $stubRequest->getQueryParams();

        $this->assertNull($params['q']);

        /** @var array<string, mixed> */
        $parsed = $stubRequest->getParsedBody();

        $this->assertNull($parsed['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_query_param_converted()
    {
        $query = array('q' => '');

        $request = $this->createRequest('GET', $query);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        $queryParams = $handler->request()->getQueryParams();

        $this->assertNull($queryParams['q']);
    }

    /**
     * @return void
     */
    public function test_passed_if_undefined_string_parsed()
    {
        $data = array('name' => 'undefined');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertNull($parsed['name']);
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $this->self = new NullString;
    }
}
