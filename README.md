# Rest Resource
HATEOAS-compatible Restful Resource descriptions, with formatters to represent 
the resources in json- or xml format.

## Installation
Install with `composer require stratadox/rest-resource`

## Example (json)
Resources formatted as json output:
```php
<?php

use Stratadox\RestResource\BasicResource;
use Stratadox\RestResource\Test\Fixture\TestRelation;
use Stratadox\RestResource\DefaultJsonFormatter;
use Stratadox\RestResource\Link;
use Stratadox\RestResource\Links;

$json = new DefaultJsonFormatter('https://a.server.somewhere/');

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
    $json->from($resource)
);
```

## Example (xml)
The same resource, now formatted as xml output:
```php
<?php

use Stratadox\RestResource\BasicResource;
use Stratadox\RestResource\Test\Fixture\TestRelation;
use Stratadox\RestResource\DefaultXmlFormatter;
use Stratadox\RestResource\Link;
use Stratadox\RestResource\Links;

$xml = new DefaultXmlFormatter('https://a.server.somewhere/');

$resource = new BasicResource(
    'hateoas-resource',
    ['foo' => 'bar'],
    Links::provide(
        Link::to('foo/1', new TestRelation('Foo'))
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
    $xml->from($resource)
);
```

## Singularisation
Formatting an xml document based on just an array structure is slightly more 
challenging than converting to json.

For example, given the input:
```php
[
    'people' => [
        [
            'id' => 1,
            'name' => 'Alice',
        ],
        [
            'id' => 2,
            'name' => 'Bob',
        ],
    ]
];
```

In json, one might have output like this:
```json
{
  "people": [
    {
      "id": 1,
      "name": "Alice"
    },
    {
      "id": 2,
      "name": "Bob"
    }
  ]
}
```

However, we'd expect from xml something in the genre of:
```xml
<people>
    <person>
        <id>1</id>
        <name>Alice</name>
    </person>
    <person>
        <id>2</id>
        <name>Bob</name>
    </person>
</people>
```

By default, the xml formatter uses [inflection](https://github.com/ICanBoogie/Inflector) 
to transform plurals into singular versions. As such, the aforementioned php 
array structure would indeed produce the expected xml.
Any language supported by the inflector can be used, for example:

```php
<?php

use Stratadox\RestResource\BasicResource;
use Stratadox\RestResource\DefaultXmlFormatter;
use Stratadox\RestResource\Links;

$xml = DefaultXmlFormatter::in('fr', '/');

$resource = new BasicResource(
    'resource-travaux',
    ['travaux' => ['foo', 'bar', 'baz']],
    Links::none()
);

$this->assertXmlStringEqualsXmlString(
    '<?xml version="1.0"?>
    <resource-travaux>
        <travaux>
            <travail>foo</travail>
            <travail>bar</travail>
            <travail>baz</travail>
        </travaux>
    </resource-travaux>',
    $xml->from($resource)
);
```

Any singularizer can be used. 
If you don't wish to run the risk of using terms that cannot be transformed into 
singular versions, the basic singularizer might be an option, although it would 
produce xml like the following example:

```php
<?php

use Stratadox\RestResource\BasicResource;
use Stratadox\RestResource\BasicSingularizer;
use Stratadox\RestResource\DefaultXmlFormatter;
use Stratadox\RestResource\Links;

$xml = new DefaultXmlFormatter('/', new BasicSingularizer());

$resource = new BasicResource(
    'people-resource',
    ['people' => [
        ['id' => 1, 'name' => 'Alice'],
        ['id' => 2, 'name' => 'Bob'],
    ]],
    Links::none()
);

$this->assertXmlStringEqualsXmlString(
    '<?xml version="1.0"?>
    <people-resource>
        <people>
            <item>
                <id>1</id>
                <name>Alice</name>
            </item>
            <item>
                <id>2</id>
                <name>Bob</name>
            </item>
        </people>
    </people-resource>',
    $xml->from($resource)
);
```
