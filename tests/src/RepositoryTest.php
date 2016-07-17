<?php namespace Nine\Collections;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */
use Nine\Collections\Exceptions\RepositoryAppendNotPossibleException;
use Nine\Collections\Exceptions\RepositoryPrependNotPossibleException;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Repository */
    protected $repository;

    public function setUp()
    {
        $this->repository = new Repository(include __DIR__ . '/config/view.php');
    }

    public function test_append()
    {
        $this->repository->append('markdown.defaults.template_paths', 'a-simulated-path');
        $target = $this->repository->get('markdown.defaults.template_paths');
        static::assertEquals('a-simulated-path',
            $this->repository->get('markdown.defaults.template_paths')[count($target) - 1]);

        // here we try to fail by attempting to prepend to a scalar value
        $this->expectException(RepositoryAppendNotPossibleException::class);
        $this->repository->append('blade.enabled', 'a-doomed-value');
    }

    public function test_array_assignment()
    {
        $this->repository['thingy'] = ['this'];

        static::assertArrayHasKey('thingy', $this->repository);
        static::assertTrue($this->repository->has('thingy'));
        static::assertEquals(['this'], $this->repository->get('thingy'));
        static::assertEquals(['this'], $this->repository['thingy']);

        unset($this->repository['thingy']);
        static::assertNull($this->repository['thingy']);

        $this->repository->set('a.compound.key', 'a regular string');
        static::assertEquals('a regular string', $this->repository['a.compound.key']);

        $this->repository->set(tuples('one:1, two:2, three:3'));
        static::assertEquals(1, $this->repository['one']);
        static::assertEquals(2, $this->repository['two']);
        static::assertEquals(3, $this->repository['three']);
    }

    public function test_all()
    {
        $repo = new Repository(tuples('one:1, two:2, three:3'));
        static::assertEquals(tuples('one:1, two:2, three:3'), $repo->all());
    }

    public function test_prepend()
    {
        $this->repository->prepend('markdown.defaults.template_paths', 'a-simulated-path');
        static::assertEquals('a-simulated-path', $this->repository->get('markdown.defaults.template_paths')[0]);

        // here we try to fail by attempting to prepend to a scalar value
        $this->expectException(RepositoryPrependNotPossibleException::class);
        $this->repository->prepend('blade.enabled', 'a-doomed-value');
    }

}
