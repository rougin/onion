<?php

namespace Rougin\Onion;

use Rougin\Onion\Fixture\HandlerStub;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class FormParserTest extends Testcase
{
    /**
     * @var \Rougin\Onion\FormParser
     */
    protected $self;

    /**
     * @return void
     */
    public function test_passed_if_amp_stripped_from_keys()
    {
        $GLOBALS['_php_input'] = 'name&amp;age=30';

        $request = $this->createRequest('POST');

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertArrayHasKey('age', $parsed);

        $this->assertEquals('30', $parsed['age']);
    }

    /**
     * @return void
     */
    public function test_passed_if_empty_body_preserves()
    {
        $data = array('existing' => 'data');

        $GLOBALS['_php_input'] = '';

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals(array('existing' => 'data'), $parsed);
    }

    /**
     * @return void
     */
    public function test_passed_if_form_parser_merges()
    {
        $GLOBALS['_php_input'] = '{"age":30}';

        $request = $this->createRequest('POST', array(), array('name' => 'John'));

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('John', $parsed['name']);

        $this->assertEquals(30, $parsed['age']);
    }

    /**
     * @return void
     */
    public function test_passed_if_form_urlencoded_parsed()
    {
        $GLOBALS['_php_input'] = 'name=John&age=30';

        $request = $this->createRequest('POST');

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('John', $parsed['name']);

        $this->assertEquals('30', $parsed['age']);
    }

    /**
     * @return void
     */
    public function test_passed_if_json_body_parsed()
    {
        $GLOBALS['_php_input'] = '{"name":"John","age":30}';

        $request = $this->createRequest('POST');

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('John', $parsed['name']);

        $this->assertEquals(30, $parsed['age']);
    }

    /**
     * @return void
     */
    public function test_passed_if_non_json_falls_back()
    {
        $GLOBALS['_php_input'] = 'not valid json';

        $request = $this->createRequest('POST');

        $handler = new HandlerStub;

        $this->self->process($request, $handler);

        // parse_str will treat this as a key with no value
        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertArrayHasKey('not_valid_json', $parsed);
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $this->self = new FormParser;

        unset($GLOBALS['_php_input']);
    }

    /**
     * @return void
     */
    protected function doTearDown()
    {
        unset($GLOBALS['_php_input']);
    }
}
