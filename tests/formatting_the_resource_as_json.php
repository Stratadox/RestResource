<?php declare(strict_types=1);

namespace Stratadox\RestResource\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\RestResource\BasicResource;
use Stratadox\RestResource\Test\Fixture\HateoasResource;
use Stratadox\RestResource\Test\Fixture\MinimalResource;
use Stratadox\RestResource\Test\Fixture\NoJson;
use Stratadox\RestResource\Test\Fixture\TestRelation;
use Stratadox\RestResource\DefaultJsonFormatter;
use Stratadox\RestResource\Link;
use Stratadox\RestResource\Links;
use Stratadox\RestResource\ResourceFormatter;
use Stratadox\RestResource\Unformattable;

/**
 * @testdox formatting the resource as json
 */
class formatting_the_resource_as_json extends TestCase
{
    /** @var ResourceFormatter */
    private $json;

    protected function setUp(): void
    {
        $this->json = new DefaultJsonFormatter('server/');
    }

    /** @test */
    function formatting_a_minimal_resource()
    {
        $resource = new MinimalResource(['foo' => 'bar']);

        $this->assertJsonStringEqualsJsonString(
            '{"minimal-resource": {"foo": "bar"}}',
            $this->json->from($resource)
        );
    }

    /** @test */
    function formatting_a_minimal_resource_with_quotes_in_the_value()
    {
        $resource = new MinimalResource(['foo' => '"bar"']);

        $this->assertJsonStringEqualsJsonString(
            '{"minimal-resource": {"foo": "\"bar\""}}',
            $this->json->from($resource)
        );
    }

    /** @test */
    function formatting_a_minimal_resource_with_quotes_in_the_key()
    {
        $resource = new MinimalResource(['"foo"' => 'bar']);

        $this->assertJsonStringEqualsJsonString(
            '{"minimal-resource": {"\"foo\"": "bar"}}',
            $this->json->from($resource)
        );
    }

    /** @test */
    function formatting_a_minimal_resource_with_a_list()
    {
        $resource = new MinimalResource(['words' => ['foo', 'bar', 'baz']]);

        $this->assertJsonStringEqualsJsonString(
            '{"minimal-resource": {"words": ["foo", "bar", "baz"]}}',
            $this->json->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_a_link()
    {
        $resource = new BasicResource(
            'hateoas-resource',
            ['foo' => 'bar'],
            Links::provide(
                Link::to('foo/1', new TestRelation('Foo'))
            )
        );

        $this->assertJsonStringEqualsJsonString(
            '{
                "hateoas-resource": {
                    "foo": "bar",
                    "links": [
                        {
                            "href": "server\/foo\/1",
                            "rel": "Foo",
                            "type": "GET"
                        }
                    ]
                }
            }',
            $this->json->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_two_links()
    {
        $resource = new HateoasResource(
            ['why' => 'not', 'more' => 'stuff'],
            Links::provide(
                Link::to('foo/1', new TestRelation('Foo')),
                Link::to('bar', new TestRelation('Bar'))
            )
        );

        $this->assertJsonStringEqualsJsonString(
            '{
                "hateoas-resource": {
                    "why": "not",
                    "more": "stuff",
                    "links": [
                        {
                            "href": "server\/foo\/1",
                            "rel": "Foo",
                            "type": "GET"
                        },
                        {
                            "href": "server\/bar",
                            "rel": "Bar",
                            "type": "GET"
                        }
                    ]
                }
            }',
            $this->json->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_mutative_action()
    {
        $resource = new HateoasResource(
            ['hello' => 'goodbye'],
            Links::provide(
                Link::to('do/something', new TestRelation('Foo', false))
            )
        );

        $this->assertJsonStringEqualsJsonString(
            '{
                "hateoas-resource": {
                    "hello": "goodbye",
                    "links": [
                        {
                            "href": "server\/do\/something",
                            "rel": "Foo",
                            "type": "POST"
                        }
                    ]
                }
            }',
            $this->json->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_overwritten_links()
    {
        $resource = new HateoasResource(
            ['links' => []],
            Links::provide(
                Link::to('foo/1', new TestRelation('Foo')),
                Link::to('bar', new TestRelation('Bar'))
            )
        );

        $this->assertJsonStringEqualsJsonString(
            '{
                "hateoas-resource": {
                    "links": []
                }
            }',
            $this->json->from($resource)
        );
    }

    /** @test */
    function not_formatting_resources_that_cannot_be_converted_to_json()
    {
        $resource = new BasicResource(
            'bad-resource',
            ['bad' => new NoJson()],
            Links::none()
        );

        $this->expectException(Unformattable::class);

        $this->json->from($resource);
    }

    /** @test */
    function not_formatting_resources_with_circular_references()
    {
        $array = [];
        $array[] = &$array;

        $resource = new BasicResource(
            'bad-resource',
            ['bad' => $array],
            Links::none()
        );

        $this->expectException(Unformattable::class);
        $this->expectExceptionMessage(
            'Could not format the resource `bad-resource` as json, because: ' .
            'Recursion detected'
        );

        $this->json->from($resource);
    }
}
