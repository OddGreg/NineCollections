<?php namespace Nine\Collections;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class GlobalScopeTest extends \PHPUnit_Framework_TestCase
{
    /** @var GlobalScope */
    protected $gscope;

    public function setUp()
    {
        $this->gscope = new GlobalScope;
    }

    public function test_instance()
    {
        static::assertInstanceOf(Scope::class, $this->gscope);
        static::assertEquals(TRUE, $this->gscope['developing']);
    }

    public function test_with_environment()
    {
        $gscope = new GlobalScope(new Environment(ROOT));
        static::assertEquals(TRUE, $this->gscope['developing']);
    }

}
