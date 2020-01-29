<?php declare(strict_types=1);

namespace Stratadox\RestResource\Test;

use PHPUnit\Framework\TestCase;
use stdClass;
use Stratadox\RestResource\BasicResource;
use Stratadox\RestResource\BasicSingularizer;
use Stratadox\RestResource\Test\Fixture\HateoasResource;
use Stratadox\RestResource\Test\Fixture\MinimalResource;
use Stratadox\RestResource\Test\Fixture\Profile;
use Stratadox\RestResource\Test\Fixture\TestRelation;
use Stratadox\RestResource\CondensedXmlFormatter;
use Stratadox\RestResource\Link;
use Stratadox\RestResource\Links;
use Stratadox\RestResource\ResourceFormatter;
use Stratadox\RestResource\Unformattable;

/**
 * @testdox formatting the resource as condensed xml
 */
class formatting_the_resource_as_condensed_xml extends TestCase
{
    /** @var ResourceFormatter */
    private $xml;

    protected function setUp(): void
    {
        $this->xml = new CondensedXmlFormatter('http://foo/');
    }

    /** @test */
    function formatting_a_minimal_resource()
    {
        $resource = new MinimalResource(['foo' => 'bar']);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource foo="bar" />',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_basic_resource()
    {
        $resource = new BasicResource(
            'hateoas-resource',
            ['foo' => 'bar'],
            Links::provide(
                Link::to('foo/1', new TestRelation('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource foo="bar">
              <links>
                <link href="http://foo/foo/1" rel="Foo" type="GET" />
              </links>
            </hateoas-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_minimal_resource_with_tags_in_the_value()
    {
        $resource = new MinimalResource(['foo' => '<bar>']);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource foo="&lt;bar&gt;" />',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_minimal_resource_with_tags_in_the_key()
    {
        $resource = new MinimalResource(['<foo>' => 'bar']);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource foo="bar" />',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_minimal_resource_with_a_list()
    {
        $resource = new MinimalResource(['words' => ['foo', 'bar', 'baz']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <words>
                <word value="foo" />
                <word value="bar" />
                <word value="baz" />
              </words>
            </minimal-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_minimal_resource_with_a_list_of_people()
    {
        $resource = new MinimalResource(['people' => ['Alice', 'Bob', 'Charlie']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <people>
                <person value="Alice" />
                <person value="Bob" />
                <person value="Charlie" />
              </people>
            </minimal-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_basic_resource_with_a_list_of_people()
    {
        $resource = new BasicResource(
            'people-resource',
            ['people' => ['Alice', 'Bob', 'Charlie']],
            Links::none()
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <people-resource>
              <people>
                <person value="Alice" />
                <person value="Bob" />
                <person value="Charlie" />
              </people>
            </people-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_a_link()
    {
        $resource = new HateoasResource(
            ['foo' => 'bar'],
            Links::provide(
                Link::to('foo/1', new TestRelation('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource foo="bar">
              <links>
                <link href="http://foo/foo/1" rel="Foo" type="GET" />
              </links>
            </hateoas-resource>',
            $this->xml->from($resource)
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

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource why="not" more="stuff">
              <links>
                <link href="http://foo/foo/1" rel="Foo" type="GET" />
                <link href="http://foo/bar" rel="Bar" type="GET" />
              </links>
            </hateoas-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_basic_resource_with_a_list_of_people_and_links()
    {
        $resource = new BasicResource(
            'people-resource',
            [
                'people' => [
                    ['name' => 'Alice'],
                    ['name' => 'Bob'],
                    ['name' =>'Charlie'],
                ],
            ],
            Links::provide(
                Link::to('people/Alice', Profile::view()),
                Link::to('people/Bob', Profile::view()),
                Link::to('people/Charlie', Profile::view())
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <people-resource>
              <people>
                <person name="Alice" />
                <person name="Bob" />
                <person name="Charlie" />
              </people>
              <links>
                <link href="http://foo/people/Alice" rel="who" type="GET" />
                <link href="http://foo/people/Bob" rel="who" type="GET" />
                <link href="http://foo/people/Charlie" rel="who" type="GET" />
              </links>
            </people-resource>',
            $this->xml->from($resource)
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

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource hello="goodbye">
              <links>
                <link href="http://foo/do/something" rel="Foo" type="POST" />
              </links>
            </hateoas-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_French_key_names()
    {
        $xml = CondensedXmlFormatter::in('french', 'http://foo/');
        $resource = new MinimalResource(['travaux' => ['foo', 'bar', 'baz']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <travaux>
                <travail value="foo" />
                <travail value="bar" />
                <travail value="baz" />
              </travaux>
            </minimal-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_Spanish_key_names()
    {
        $xml = CondensedXmlFormatter::in('spanish', 'http://foo/');
        $resource = new MinimalResource(['conversaciones' => ['foo', 'bar', 'baz']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <conversaciones>
                <conversación value="foo" />
                <conversación value="bar" />
                <conversación value="baz" />
              </conversaciones>
            </minimal-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_Turkish_key_names()
    {
        $xml = CondensedXmlFormatter::in('Turkish', 'http://foo/');
        $resource = new MinimalResource(['kitaplar' => ['foo', 'bar', 'baz']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <kitaplar>
                <kitap value="foo" />
                <kitap value="bar" />
                <kitap value="baz" />
              </kitaplar>
            </minimal-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_Gibberish_key_names()
    {
        $xml = CondensedXmlFormatter::in('french', 'http://foo/');
        $resource = new MinimalResource(['foo' => ['bar', 'baz']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <foo>
                <foo value="bar" />
                <foo value="baz" />
              </foo>
            </minimal-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_link_and_French_key_names()
    {
        $xml = CondensedXmlFormatter::in('FR', 'http://foo/');
        $resource = new HateoasResource(
            ['travaux' => [['nom' => 'foo'], ['nom' => 'bar']]],
            Links::provide(
                Link::to('foo', new TestRelation('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <travaux>
                <travail nom="foo" />
                <travail nom="bar" />
              </travaux>
              <links>
                <link href="http://foo/foo" rel="Foo" type="GET" />
              </links>
            </hateoas-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_without_inflection_magic()
    {
        $xml = new CondensedXmlFormatter('http://foo/', new BasicSingularizer());
        $resource = new HateoasResource(
            [
                'onlar' => ['Alice', 'Alfred', 'Bob', 'Barbara', 'Charlie', 'Christina'],
                'messieurs' => ['Alfred', 'Bob', 'Charlie'],
                'mesdames' => ['Alice', 'Barbara', 'Christina'],
            ],
            Links::provide(
                Link::to('foo', new TestRelation('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <onlar>
                <item value="Alice" />
                <item value="Alfred" />
                <item value="Bob" />
                <item value="Barbara" />
                <item value="Charlie" />
                <item value="Christina" />
              </onlar>
              <messieurs>
                <item value="Alfred" />
                <item value="Bob" />
                <item value="Charlie" />
              </messieurs>
              <mesdames>
                <item value="Alice"/>
                <item value="Barbara"/>
                <item value="Christina"/>
              </mesdames>
              <links>
                <link href="http://foo/foo" rel="Foo" type="GET" />
              </links>
            </hateoas-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_a_lot_of_nested_elements()
    {
        $resource = new MinimalResource([
            'lists' => [
                [1, 2, 3, 4],
                ['A', 'B', 'C'],
                [['a' => 1], ['b' => 2], ['c' => 3]],
                [[1, 2, 3], [1, 2, 3]],
            ]
        ]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <lists>
                <list>
                  <item value="1" />
                  <item value="2" />
                  <item value="3" />
                  <item value="4" />
                </list>
                <list>
                  <item value="A" />
                  <item value="B" />
                  <item value="C" />
                </list>
                <list>
                  <item a="1" />
                  <item b="2" />
                  <item c="3" />
                </list>
                <list>
                  <item>
                    <item value="1" />
                    <item value="2" />
                    <item value="3" />
                  </item>
                  <item>
                    <item value="1" />
                    <item value="2" />
                    <item value="3" />
                  </item>
                </list>
              </lists>
            </minimal-resource>',
            $this->xml->from($resource)
        );
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

        $this->xml->from($resource);
    }

    /** @test */
    function not_formatting_resources_with_invalid_values()
    {
        $resource = new BasicResource(
            'bad-resource',
            ['bad' => new stdClass()],
            Links::none()
        );

        $this->expectException(Unformattable::class);
        $this->expectExceptionMessage(
            'Could not format the resource `bad-resource` as xml, because: Object'
        );

        $this->xml->from($resource);
    }
}
