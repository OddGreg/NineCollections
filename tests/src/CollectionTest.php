<?php namespace Nine\Collections;

    /**
     * @package Nine
     * @version 0.4.2
     * @author  Greg Truesdell <odd.greg@gmail.com>
     */

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Collection */
    private $collection;

    public function setUp()
    {
        $config = Config::createFromYaml(__DIR__ . '/config/config.yml');
        $this->collection = new Collection($config->copy());
    }

    public function tearDown()
    {
        $this->collection = NULL;
    }

    public function testKeys()
    {
        $keys = $this->collection->keys();
        static::assertEquals(['app', 'db', 'test'], $keys->toArray());
    }

    public function test_all_and_chunk()
    {
        // validate ->all()
        static::assertArrayHasKey('app', $this->collection->all());

        // validate ->chunk()
        $collection = new Collection($this->collection->all());
        /** @var array $chunks */
        $chunks = $collection->chunk(1);

        static::assertInstanceOf(Collection::class, $chunks[0], 'chunk [0] should be a Collection.');
        static::assertArrayHasKey('app', $chunks[0], 'chunk [0] should have the `app` key.');

        static::assertInstanceOf(Collection::class, $chunks[1], 'chunk [1] should be a Collection.');
        static::assertArrayNotHasKey('app', $chunks[1], 'chunk [1] should NOT have the `app` key.');
        static::assertArrayHasKey('db', $chunks[1], 'chunk [1] should have the `db` key.');

        static::assertInstanceOf(Collection::class, $chunks[2], 'chunk [2] should be a Collection.');
        static::assertArrayNotHasKey('db', $chunks[2], 'chunk [2] should NOT have the `db` key.');
        static::assertArrayHasKey('test', $chunks[2], 'chunk [2] should have the `test` key.');
    }

    public function test_collapse_and_contains()
    {
        // validate ->collapse()
        $collection = new Collection($this->collection);

        // collapse the array structure by merging first order keys
        $collapsed = $collection->collapse();

        // the `db` key should now be gone
        static::assertArrayNotHasKey('db', $collapsed, 'collapse should merged the `db` key.');
        // the `models` key should now be a first-order key
        static::assertArrayHasKey('models', $collapsed, 'collapse should now have `models` as a first-order key.');

        $collect = new Collection([
            $config = new Config(CONFIG),
            $collection,
            $paths = new Paths,
        ]);

        static::assertTrue($collect->contains($paths), 'collection should contain specific instance of Paths');
        $collect = NULL;
    }

    public function test_collection_contents()
    {
        static::assertArrayHasKey('app', $this->collection, 'setUp should have imported `app` from the config.yml file.');
        static::assertArrayHasKey('db', $this->collection, 'setUp should have imported `db` from the config.yml file.');
        static::assertArrayHasKey('test', $this->collection, 'setUp should have imported `test` from the config.yml file.');
    }

    public function test_comparisons()
    {
        static::assertTrue($this->collection->contains(function ($key, $value) {
            return $key === 'app' ? $value : false;
        }));
    }

    public function test_count_first_last()
    {
        $collect = new Collection([
            $config = new Config(CONFIG),
            $paths = new Paths,
        ]);

        static::assertSame($collect->count(), 2, 'there should be exactly 3 items in the collection.');
        static::assertInstanceOf(Config::class, $collect->first(), 'first collection item should be instance of Config');
        static::assertInstanceOf(Paths::class, $collect->last(), 'last collection item should be instance of Path');
    }

    public function test_exports()
    {
        // the collection emits a json string when coerced to a string.
        static::assertEquals(count($this->collection->toJson()), count((string) $this->collection));

        static::assertTrue($this->collection->forPage(1, 2)->has('app'));
        static::assertTrue($this->collection->forPage(1, 2)->has('db'));
        static::assertTrue($this->collection->forPage(2, 2)->has('test'));

        static::assertEquals($this->collection->all()['app'], $this->collection->first());
        static::assertEquals($this->collection->all()['test'], $this->collection->last());

        static::assertInstanceOf(\ArrayIterator::class, $this->collection->getIterator());
    }

    public function test_where()
    {
        $collection = new Collection([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ]);

        $filtered = $collection->where('price', 100);

        $expected = [
            1 => ['product' => 'Chair', 'price' => 100,],
            3 => ['product' => 'Door', 'price' => 100,],
        ];

        static::assertEquals($expected, $filtered->all());
    }

}
