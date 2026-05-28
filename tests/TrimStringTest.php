<?php

namespace Rougin\Onion;

use Rougin\Onion\Fixture\HandlerStub;
use Rougin\Slytherin\Http\Response;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class TrimStringTest extends Testcase
{
    /**
     * @var \Rougin\Onion\TrimString
     */
    protected $self;

    /**
     * @return void
     */
    public function test_passed_if_integer_unchanged()
    {
        $data = array('age' => 42);

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

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
        $data = array('item' => array('a' => '  hello  ', 'b' => 'world', 'c' => 99));

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        /** @var array<string, mixed> $parsedItem */
        $parsedItem = $parsed['item'];

        $this->assertEquals('hello', $parsedItem['a']);

        $this->assertEquals('world', $parsedItem['b']);

        $this->assertEquals(99, $parsedItem['c']);
    }

    /**
     * @return void
     */
    public function test_passed_if_nested_value_trimmed()
    {
        $data = array('filter' => array('name' => '  John  '));

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        /** @var array<string, mixed> $parsedFilter */
        $parsedFilter = $parsed['filter'];

        $this->assertEquals('John', $parsedFilter['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_normal_string_unchanged()
    {
        $data = array('name' => 'hello');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('hello', $parsed['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_query_and_body_trimmed()
    {
        $query = array('q' => '  search  ');

        $data = array('name' => '  John  ');

        $request = $this->createRequest('POST', $query, $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        $stubRequest = $handler->request();

        $params = $stubRequest->getQueryParams();

        $this->assertEquals('search', $params['q']);

        /** @var array<string, mixed> */
        $parsed = $stubRequest->getParsedBody();

        $this->assertEquals('John', $parsed['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_query_param_trimmed()
    {
        $query = array('q' => '  hello  ');

        $request = $this->createRequest('GET', $query);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        $queryParams = $handler->request()->getQueryParams();

        $this->assertEquals('hello', $queryParams['q']);
    }

    /**
     * @return void
     */
    public function test_passed_if_string_trimmed()
    {
        $data = array('name' => '  John  ');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('John', $parsed['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_whitespace_only_becomes_empty()
    {
        $data = array('name' => '   ');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('', $parsed['name']);
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $this->self = new TrimString;
    }
}
