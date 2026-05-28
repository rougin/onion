<?php

namespace Rougin\Onion;

use Rougin\Onion\Fixture\HandlerStub;
use Rougin\Slytherin\Http\Response;

/**
 * @package Onion
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class BodyParamsTest extends Testcase
{
    /**
     * @var \Rougin\Onion\BodyParams
     */
    protected $self;

    /**
     * @return void
     */
    public function test_passed_if_body_merged_with_existing()
    {
        $GLOBALS['_php_input'] = 'age=30';

        $request = $this->createRequest('PUT', array(), array('name' => 'John'));

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('John', $parsed['name']);

        $this->assertEquals('30', $parsed['age']);
    }

    /**
     * @return void
     */
    public function test_passed_if_delete_parsed()
    {
        $GLOBALS['_php_input'] = 'name=John&age=30';

        $request = $this->createRequest('DELETE');

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('John', $parsed['name']);

        $this->assertEquals('30', $parsed['age']);
    }

    /**
     * @return void
     */
    public function test_passed_if_empty_body_preserves_data()
    {
        $GLOBALS['_php_input'] = '';

        $request = $this->createRequest('DELETE', array(), array('existing' => 'data'));

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals(array('existing' => 'data'), $parsed);
    }

    /**
     * @return void
     */
    public function test_passed_if_get_skipped()
    {
        $data = array('original' => 'data');

        $request = $this->createRequest('GET', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals(array('original' => 'data'), $parsed);
    }

    /**
     * @return void
     */
    public function test_passed_if_input_overrides_existing()
    {
        $GLOBALS['_php_input'] = 'name=Jane';

        $request = $this->createRequest('PUT', array(), array('name' => 'John'));

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('Jane', $parsed['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_multipart_empty_boundary()
    {
        $GLOBALS['_php_input'] = 'form-data';

        $request = $this->createRequest('PUT');

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals(array(), $parsed);
    }

    /**
     * @return void
     */
    public function test_passed_if_multipart_file_uploaded()
    {
        $boundary = '----UploadBoundary';

        $body = '--' . $boundary . "\r\n";

        $body .= 'Content-Disposition: form-data; name="file"; filename="test.txt"' . "\r\n";

        $body .= 'Content-Type: text/plain' . "\r\n";

        $body .= "\r\n";

        $body .= 'file contents here' . "\r\n";

        $body .= '--' . $boundary . '--' . "\r\n";

        $GLOBALS['_php_input'] = $body;

        $request = $this->createRequest('PUT');

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertArrayHasKey('file', $parsed);

        /** @var array<string, mixed> */
        $file = $parsed['file'];

        $this->assertEquals(0, $file['error']);

        $this->assertEquals('test.txt', $file['name']);

        $this->assertEquals('text/plain', $file['type']);

        $this->assertNotEmpty($file['tmp_name']);

        $this->assertEquals(strlen('file contents here'), $file['size']);
    }

    /**
     * @return void
     */
    public function test_passed_if_multipart_nested_keys()
    {
        $boundary = '----NestedBoundary';

        $body = '--' . $boundary . "\r\n";

        $body .= 'Content-Disposition: form-data; name="fields[0][name]"' . "\r\n";

        $body .= "\r\n";

        $body .= 'John' . "\r\n";

        $body .= '--' . $boundary . '--' . "\r\n";

        $GLOBALS['_php_input'] = $body;

        $request = $this->createRequest('PUT');

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        /** @var array<int, mixed> $fields */
        $fields = $parsed['fields'];

        /** @var array<string, mixed> $first */
        $first = $fields[0];

        $this->assertEquals('John', $first['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_multipart_no_name_field()
    {
        $boundary = '----SkipBoundary';

        $body = '--' . $boundary . "\r\n";

        $body .= 'Content-Disposition: form-data' . "\r\n";

        $body .= "\r\n";

        $body .= 'ignored-value' . "\r\n";

        $body .= '--' . $boundary . "\r\n";

        $body .= 'Content-Disposition: form-data; name="name"' . "\r\n";

        $body .= "\r\n";

        $body .= 'John' . "\r\n";

        $body .= '--' . $boundary . '--' . "\r\n";

        $GLOBALS['_php_input'] = $body;

        $request = $this->createRequest('PUT');

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals(array('name' => 'John'), $parsed);
    }

    /**
     * @return void
     */
    public function test_passed_if_multipart_parsed()
    {
        $boundary = '----TestBoundary';

        $body = '--' . $boundary . "\r\n";

        $body .= 'Content-Disposition: form-data; name="name"' . "\r\n";

        $body .= "\r\n";

        $body .= 'John' . "\r\n";

        $body .= '--' . $boundary . "\r\n";

        $body .= 'Content-Disposition: form-data; name="age"' . "\r\n";

        $body .= "\r\n";

        $body .= '30' . "\r\n";

        $body .= '--' . $boundary . '--' . "\r\n";

        $GLOBALS['_php_input'] = $body;

        $request = $this->createRequest('PUT');

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('John', $parsed['name']);

        $this->assertEquals('30', $parsed['age']);
    }

    /**
     * @return void
     */
    public function test_passed_if_multipart_unmatched_header()
    {
        $boundary = '----UnmatchedBoundary';

        $body = '--' . $boundary . "\r\n";

        $body .= 'Content-Disposition: form-data; unmatched' . "\r\n";

        $body .= "\r\n";

        $body .= 'value' . "\r\n";

        $body .= '--' . $boundary . '--' . "\r\n";

        $GLOBALS['_php_input'] = $body;

        $request = $this->createRequest('PUT');

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals(array(), $parsed);
    }

    /**
     * @return void
     */
    public function test_passed_if_patch_parsed()
    {
        $GLOBALS['_php_input'] = 'status=active';

        $request = $this->createRequest('PATCH');

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('active', $parsed['status']);
    }

    /**
     * @return void
     */
    public function test_passed_if_post_skipped()
    {
        $data = array('original' => 'data');

        $request = $this->createRequest('POST', array(), $data);

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals(array('original' => 'data'), $parsed);
    }

    /**
     * @return void
     */
    public function test_passed_if_put_parsed()
    {
        $GLOBALS['_php_input'] = 'title=Hello';

        $request = $this->createRequest('PUT');

        $handler = new HandlerStub(new Response);

        $this->self->process($request, $handler);

        /** @var array<string, mixed> */
        $parsed = $handler->request()->getParsedBody();

        $this->assertEquals('Hello', $parsed['title']);
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $this->self = new BodyParams;

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
