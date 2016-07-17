<?php namespace Nine\Collections;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /** @var Environment */
    private $environment;

    public function setUp()
    {
        $this->environment = new Environment(ROOT, 'APP_ENV');
        $this->environment->detectEnvironment();
    }

    public function testEnvironment()
    {
        static::assertArrayHasKey('app_key', $this->environment->detectEnvironment());
        static::assertEquals(TRUE, $this->environment->get('DEBUG'));
        static::assertEquals(TRUE, $this->environment->get('debugging'));
    }

    public function test_fail_detect_environment()
    {
        $environment = new Environment(ROOT, 'Blah');
        $this->expectException(Exceptions\InvalidEnvironmentKeyException::class);
        $environment->detectEnvironment();
    }

    public function test_get_environment_key()
    {
        static::assertEquals('APP_ENV', $this->environment->getEnvironmentKey());
    }

}
