<?php declare(strict_types=1);

namespace Stratadox\RestResource\Test;

use ICanBoogie\Inflector;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stratadox\RestResource\BasicResource;
use Stratadox\RestResource\BasicSingularizer;
use Stratadox\RestResource\Test\Fixture\HateoasResource;
use Stratadox\RestResource\Test\Fixture\MinimalResource;
use Stratadox\RestResource\Test\Fixture\Profile;
use Stratadox\RestResource\BoogieSingularizer;
use Stratadox\RestResource\DefaultXmlFormatter;
use Stratadox\RestResource\Link;
use Stratadox\RestResource\Links;
use Stratadox\RestResource\ResourceFormatter;
use Stratadox\RestResource\Type;
use Stratadox\RestResource\Unformattable;

/**
 * @testdox formatting the resource as xml
 */
class formatting_the_resource_as_xml extends TestCase
{
    /** @var ResourceFormatter */
    private $xml;

    protected function setUp(): void
    {
        $this->xml = new DefaultXmlFormatter('server/');
    }

    /** @test */
    function formatting_a_minimal_resource()
    {
        $resource = new MinimalResource(['foo' => 'bar']);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <foo>bar</foo>
            </minimal-resource>',
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
                Link::to('foo/1', Type::get('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <foo>bar</foo>
              <links>
                <link>
                  <href>server/foo/1</href>
                  <rel>Foo</rel>
                  <type>GET</type>
                </link>
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
            <minimal-resource>
              <foo>&lt;bar&gt;</foo>
            </minimal-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_minimal_resource_with_tags_in_the_key()
    {
        $resource = new MinimalResource(['<foo>' => 'bar']);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <foo>bar</foo>
            </minimal-resource>',
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
                <word>foo</word>
                <word>bar</word>
                <word>baz</word>
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
                <person>Alice</person>
                <person>Bob</person>
                <person>Charlie</person>
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
                <person>Alice</person>
                <person>Bob</person>
                <person>Charlie</person>
              </people>
            </people-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_nested_resource()
    {
        $resource = new BasicResource(
            'nested-resource',
            ['children' => [
                new BasicResource('child-resource', ['n' => 1], Links::none()),
                new BasicResource('child-resource', ['n' => 2], Links::none()),
                new BasicResource('child-resource', ['n' => 3], Links::none()),
            ]],
            Links::none()
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <nested-resource>
              <children>
                <child><child-resource><n>1</n></child-resource></child>
                <child><child-resource><n>2</n></child-resource></child>
                <child><child-resource><n>3</n></child-resource></child>
              </children>
            </nested-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_twice_nested_resource()
    {
        $resource = new BasicResource(
            'nested-resource',
            ['children' => [
                new BasicResource(
                    'child-resource',
                    ['grandchildren' => [
                        new MinimalResource(['foo' => 'bar'])
                    ]],
                    Links::none()
                ),
            ]],
            Links::none()
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <nested-resource>
              <children>
                <child>
                    <child-resource>
                        <grandchildren>
                            <grandchild>
                              <minimal-resource>
                                <foo>bar</foo>
                              </minimal-resource>
                            </grandchild>
                        </grandchildren>
                    </child-resource>
                </child>
              </children>
            </nested-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_a_link()
    {
        $resource = new HateoasResource(
            ['foo' => 'bar'],
            Links::provide(
                Link::to('foo/1', Type::get('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <foo>bar</foo>
              <links>
                <link>
                  <href>server/foo/1</href>
                  <rel>Foo</rel>
                  <type>GET</type>
                </link>
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
                Link::to('foo/1', Type::get('Foo')),
                Link::to('bar', Type::get('Bar'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <why>not</why>
              <more>stuff</more>
              <links>
                <link>
                  <href>server/foo/1</href>
                  <rel>Foo</rel>
                  <type>GET</type>
                </link>
                <link>
                  <href>server/bar</href>
                  <rel>Bar</rel>
                  <type>GET</type>
                </link>
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
            ['people' => ['Alice', 'Bob', 'Charlie']],
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
                <person>Alice</person>
                <person>Bob</person>
                <person>Charlie</person>
              </people>
              <links>
                <link>
                  <href>server/people/Alice</href>
                  <rel>who</rel>
                  <type>GET</type>
                </link>
                <link>
                  <href>server/people/Bob</href>
                  <rel>who</rel>
                  <type>GET</type>
                </link>
                <link>
                  <href>server/people/Charlie</href>
                  <rel>who</rel>
                  <type>GET</type>
                </link>
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
                Link::to('do/something', Type::post('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <hello>goodbye</hello>
              <links>
                <link>
                  <href>server/do/something</href>
                  <rel>Foo</rel>
                  <type>POST</type>
                </link>
              </links>
            </hateoas-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_French_key_names()
    {
        $xml = DefaultXmlFormatter::in('french', 'server/');
        $resource = new MinimalResource(['travaux' => ['foo', 'bar', 'baz']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <travaux>
                <travail>foo</travail>
                <travail>bar</travail>
                <travail>baz</travail>
              </travaux>
            </minimal-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_Spanish_key_names()
    {
        $xml = DefaultXmlFormatter::in('spanish', 'server/');
        $resource = new MinimalResource(['conversaciones' => ['foo', 'bar', 'baz']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <conversaciones>
                <conversación>foo</conversación>
                <conversación>bar</conversación>
                <conversación>baz</conversación>
              </conversaciones>
            </minimal-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_Turkish_key_names()
    {
        $xml = DefaultXmlFormatter::in('Turkish', 'server/');
        $resource = new MinimalResource(['kitaplar' => ['foo', 'bar', 'baz']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <kitaplar>
                <kitap>foo</kitap>
                <kitap>bar</kitap>
                <kitap>baz</kitap>
              </kitaplar>
            </minimal-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_Gibberish_key_names()
    {
        $xml = DefaultXmlFormatter::in('french', 'server/');
        $resource = new MinimalResource(['foo' => ['bar', 'baz']]);

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <minimal-resource>
              <foo>
                <foo>bar</foo>
                <foo>baz</foo>
              </foo>
            </minimal-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_link_and_French_key_names()
    {
        $xml = DefaultXmlFormatter::in('FR', 'server/');
        $resource = new HateoasResource(
            ['travaux' => ['foo', 'bar', 'baz']],
            Links::provide(
                Link::to('foo', Type::get('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <travaux>
                <travail>foo</travail>
                <travail>bar</travail>
                <travail>baz</travail>
              </travaux>
              <links>
                <link>
                  <href>server/foo</href>
                  <rel>Foo</rel>
                  <type>GET</type>
                </link>
              </links>
            </hateoas-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_link_and_Turkish_key_names()
    {
        $xml = DefaultXmlFormatter::in('tr', 'server/');
        $resource = new HateoasResource(
            ['kitaplar' => ['foo', 'bar', 'baz']],
            Links::provide(
                Link::to('foo', Type::get('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <kitaplar>
                <kitap>foo</kitap>
                <kitap>bar</kitap>
                <kitap>baz</kitap>
              </kitaplar>
              <links>
                <link>
                  <href>server/foo</href>
                  <rel>Foo</rel>
                  <type>GET</type>
                </link>
              </links>
            </hateoas-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_with_link_and_both_French_and_Turkish_key_names()
    {
        $xml = new DefaultXmlFormatter('server/', new BoogieSingularizer(
            Inflector::get('tr'),
            Inflector::get('fr'),
            Inflector::get('en')
        ));
        $resource = new HateoasResource(
            [
                'onlar' => ['Alice', 'Alfred', 'Bob', 'Barbara', 'Charlie', 'Christina'],
                'messieurs' => ['Alfred', 'Bob', 'Charlie'],
                'mesdames' => ['Alice', 'Barbara', 'Christina'],
            ],
            Links::provide(
                Link::to('foo', Type::get('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <onlar>
                <o>Alice</o>
                <o>Alfred</o>
                <o>Bob</o>
                <o>Barbara</o>
                <o>Charlie</o>
                <o>Christina</o>
              </onlar>
              <messieurs>
                <monsieur>Alfred</monsieur>
                <monsieur>Bob</monsieur>
                <monsieur>Charlie</monsieur>
              </messieurs>
              <mesdames>
                <madame>Alice</madame>
                <madame>Barbara</madame>
                <madame>Christina</madame>
              </mesdames>
              <links>
                <link>
                  <href>server/foo</href>
                  <rel>Foo</rel>
                  <type>GET</type>
                </link>
              </links>
            </hateoas-resource>',
            $xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_resource_without_inflection_magic()
    {
        $xml = new DefaultXmlFormatter('server/', new BasicSingularizer());
        $resource = new HateoasResource(
            [
                'onlar' => ['Alice', 'Alfred', 'Bob', 'Barbara', 'Charlie', 'Christina'],
                'messieurs' => ['Alfred', 'Bob', 'Charlie'],
                'mesdames' => ['Alice', 'Barbara', 'Christina'],
            ],
            Links::provide(
                Link::to('foo', Type::get('Foo'))
            )
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <hateoas-resource>
              <onlar>
                <item>Alice</item>
                <item>Alfred</item>
                <item>Bob</item>
                <item>Barbara</item>
                <item>Charlie</item>
                <item>Christina</item>
              </onlar>
              <messieurs>
                <item>Alfred</item>
                <item>Bob</item>
                <item>Charlie</item>
              </messieurs>
              <mesdames>
                <item>Alice</item>
                <item>Barbara</item>
                <item>Christina</item>
              </mesdames>
              <links>
                <link>
                  <href>server/foo</href>
                  <rel>Foo</rel>
                  <type>GET</type>
                </link>
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
                  <item>1</item>
                  <item>2</item>
                  <item>3</item>
                  <item>4</item>
                </list>
                <list>
                  <item>A</item>
                  <item>B</item>
                  <item>C</item>
                </list>
                <list>
                  <item>
                    <a>1</a>
                  </item>
                  <item>
                    <b>2</b>
                  </item>
                  <item>
                    <c>3</c>
                  </item>
                </list>
                <list>
                  <item>
                    <item>1</item>
                    <item>2</item>
                    <item>3</item>
                  </item>
                  <item>
                    <item>1</item>
                    <item>2</item>
                    <item>3</item>
                  </item>
                </list>
              </lists>
            </minimal-resource>',
            $this->xml->from($resource)
        );
    }

    /** @test */
    function formatting_a_nested_resource_where_the_children_have_links()
    {
        $resource = new BasicResource(
            'nested-resource',
            ['children' => [
                new BasicResource('child-resource', ['n' => 1], Links::provide(
                    Link::to('foo', Type::get('Foo'))
                )),
                new BasicResource('child-resource', ['n' => 2], Links::provide(
                    Link::to('foo', Type::get('Foo'))
                )),
                new BasicResource('child-resource', ['n' => 3], Links::provide(
                    Link::to('foo', Type::get('Foo'))
                )),
            ]],
            Links::provide(Link::to('bar', Type::get('Bar')))
        );

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?>
            <nested-resource>
                <children>
                    <child><child-resource>
                        <n>1</n>
                        <links>
                            <link>
                              <href>server/foo</href>
                              <rel>Foo</rel>
                              <type>GET</type>
                            </link>
                        </links>
                    </child-resource></child>
                    <child><child-resource>
                        <n>2</n>
                        <links>
                            <link>
                              <href>server/foo</href>
                              <rel>Foo</rel>
                              <type>GET</type>
                            </link>
                        </links>
                    </child-resource></child>
                    <child><child-resource>
                        <n>3</n>
                        <links>
                            <link>
                              <href>server/foo</href>
                              <rel>Foo</rel>
                              <type>GET</type>
                            </link>
                        </links>
                    </child-resource></child>
                </children>
                <links>
                    <link>
                      <href>server/bar</href>
                      <rel>Bar</rel>
                      <type>GET</type>
                    </link>
                </links>
            </nested-resource>',
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
