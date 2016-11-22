<?php namespace Nine\Collections;

use InvalidArgumentException;
use Nine\Collections\Exceptions\ImmutableViolationException;
use Nine\Library\Lib;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /** @var Attributes */
    private $attributes;

    public function setUp()
    {
        $this->attributes = new Attributes(
            Config::createFromYaml(__DIR__ . '/config/config.yml')
        );
    }

    public function tearDown()
    {
        $this->attributes = NULL;
    }

    public function test_accumulate()
    {
        $attributes = new Attributes;

        $this->expectException(InvalidArgumentException::class);
        $attributes->{'not_there'};
    }

    public function test_arrayable_attributes()
    {
        $this->attributes = new Attributes(
            $arrayable = Config::createFromYaml(__DIR__ . '/config/config.yml')
        );

        static::assertEquals($this->attributes->toArray(), $arrayable->toArray());
    }

    public function test_assignments()
    {
        $attributes = new Attributes(['test' => 'test array assignment']);
        static::assertTrue(isset($this->attributes['test']));
        static::assertEquals('test array assignment', $attributes['test']);

        $this->expectException('Nine\Collections\Exceptions\ImmutableViolationException');
        unset($attributes['test']);
    }

    public function test_content()
    {
        // validate attributes
        static::assertArrayHasKey('db', $this->attributes->getAttributes(),
            'key `db` should exist in attributes.');

        // find expected
        static::assertSame(Lib::array_query($this->attributes->getAttributes(), 'db.models.home'), 'HomeModel', '`db.models.home` should return `HomeModel`.');

        //validate toJson()
        static::assertEquals(file_get_contents(__DIR__ . '/config/json_pretty_test.json'), $this->attributes->toJson(JSON_PRETTY_PRINT),
            'toJson should match stored json sample.');
    }

    public function test_copy()
    {
        $attributes = new Attributes(['a' => 1, 'b' => 2]);
        static::assertEquals(['a' => 1, 'b' => 2], $attributes->copy());
    }

    public function test_get()
    {
        $attributes = new Attributes(['a' => 1, 'b' => 2]);
        static::assertEquals(1, $attributes->get('a'));
    }

    public function test_has()
    {
        $attributes = new Attributes(['has' => 'i can has value?']);

        static::assertTrue($attributes->has('has'));
    }

    public function test_immutable_assignment()
    {
        /** @noinspection OnlyWritesOnParameterInspection */
        $attributes = new Attributes([]);

        /** @noinspection OnlyWritesOnParameterInspection */
        $attributes['test']= 'this should succeed';

        $this->expectException(ImmutableViolationException::class);

        /** @noinspection OnlyWritesOnParameterInspection */
        $attributes['test'] = 'this should not succeed';
    }

    public function test_immutable_set()
    {
        $attributes = new Attributes([]);
        $attributes->set('this', 'should succeed');
        $this->expectException(ImmutableViolationException::class);
        $attributes->set('this', 'should fail');
    }

    public function test_instance()
    {
        // validate the instance of Attributes
        static::assertInstanceOf(Attributes::class, $this->attributes);
    }

    public function test_setAttributes()
    {
        // set from an Arrayable source
        $source = new Attributes(['a' => 1, 'b' => 2]);
        $attributes = new Attributes();
        $attributes->setAttributes($source);
        static::assertEquals(1, $attributes['a']);

        // set from a JSON source
        $source = new ExportableClass(['a' => 1, 'b' => 2]);
        $attributes = new Attributes();
        $attributes->setAttributes($source);

        // set from variable or literal
        $attributes = new Attributes();
        $attributes->setAttributes(['a' => 1, 'b' => 2]);
        static::assertEquals(2, $attributes['b']);

        $this->expectException(ImmutableViolationException::class);
        // can't do this twice
        $attributes->setAttributes(['a' => 1, 'b' => 2]);
    }

}
