<?php namespace Nine\Collections;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class ScopeTest extends \PHPUnit_Framework_TestCase
{
    protected $scope;

    public function doPlugin($test)
    {
        return strtoupper($test);
    }

    public function setUp()
    {
        $this->scope = new Scope;
    }

    public function test_access()
    {
        $scope = new Scope(tuples('a:1, b:2, c:3'));
        // append
        $scope->append('d', 'extra');
        static::assertTrue($scope->has('d'));
        static::assertEquals(tuples('a:1, b:2, c:3, d:extra'), $scope->toArray());
        // get
        static::assertEquals('extra', $scope->get('d'));
        // count
        static::assertEquals(4, $scope->count());
        // set
        $scope->set('d', 'other');
        static::assertEquals('other', $scope->get('d'));
        // search and replace
        $scope->search_and_replace(['d' => 'fork']);
        static::assertEquals('fork', $scope->get('d'));
    }

    public function test_bad_call()
    {
        $scope = new Scope;
        $this->expectException(\BadFunctionCallException::class);
        $this->expectExceptionMessage('Plug-in gaga does not exist.');
        $scope->gaga();
    }

    public function test_each()
    {
        $scope = new Scope(tuples('a:1, b:2, c:3'));

        $count = 0;
        $scope->each(function ($key, $item) use (&$count) {
            $count += $key < 'c' ? $item : 0;
        });

        static::assertEquals(3, $count);

    }

    public function test_every()
    {
        $scope = new Scope(tuples('a:1, b:2, c:3'));

        static::assertEquals([1, 3], $scope->every(2)->toArray());
    }

    public function test_filter()
    {
        $scope = new Scope(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]);
        static::assertEquals(['a' => 1, 'c' => 3, 'e' => 5], $scope->filter(
            function ($item) { return $item & 1; })->toArray());
        static::assertEquals(['b' => 2, 'd' => 4,], $scope->filter(
            function ($item) { return ! ($item & 1); })->toArray());

        static::assertEquals(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5], $scope->filter()->toArray());
    }

    public function test_flip()
    {
        $scope = new Scope(tuples('a:1, b:2, c:3'));
        static::assertEquals([1 => 'a', 2 => 'b', 3 => 'c'], $scope->flip()->toArray());
    }

    public function test_instantiation()
    {
        static::assertInstanceOf(Scope::class, $this->scope);
        static::assertNotSame(new Scope, $this->scope, 'Scope should create a new instance.');
    }

    public function test_jason_serialize()
    {
        $scope = new Scope(tuples('a:1, b:2, c:3'));
        static::assertEquals('{"a":1,"b":2,"c":3}', $scope->jsonSerialize());
        static::assertEquals('{"a":1,"b":2,"c":3}', $scope->toJson());
    }

    public function test_plugins()
    {
        $scope = new Scope;
        static::assertFalse($scope->hasPlugin('test@me'));

        $scope->plugin('test.me', [$this, 'doPlugin']);
        static::assertTrue($scope->hasPlugin('test.me'));
        static::assertEquals('THIS IS A TEST', $scope->{'test.me'}('This is a test'));

        $scope->forgetPlugin('test.me');
        static::assertFalse($scope->hasPlugin('test.me'));

    }

    public function test_storage()
    {
        $scope = new Scope(new Collection(tuples('a:1, b:2, c:3')));
        static::assertEquals(tuples('a:1, b:2, c:3'), $scope->toArray());

        $scope->forget('b');
        static::assertEquals(tuples('a:1, c:3'), $scope->toArray());

        $scope->merge(tuples('b:two'));
        static::assertEquals(tuples('a:1, b:two, c:3'), $scope->toArray());
    }

}
