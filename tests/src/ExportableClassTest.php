<?php namespace Nine\tests\src;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */
use Nine\Collections\ExportableClass;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class ExportableClassTest extends \PHPUnit_Framework_TestCase
{
    /** @var ExportableClass */
    protected $exporter;

    public function setUp()
    {
        $this->exporter = new ExportableClass([
            'name'    => 'Exporter',
            'job'     => 'To export stuff.',
            'exports' => 123,
        ]);

    }

    public function test_export()
    {
        $expected = <<<EOD
{
	"name": "Exporter",
	"job": "To export stuff.",
	"exports": 123
}
EOD;
        static::assertEquals($expected,$this->exporter->exportFormattedJSON());

        $this->exporter->exportPHPFile(__DIR__ . '/temp/', 'name');
        $this->exporter->exportPHPFile(__DIR__ . '/temp/', 'job', 'jobs.php');
        //unlink(__DIR__ . '/temp/name.php');
        //unlink(__DIR__ . '/temp/jobs.php');

        $this->exporter->exportYAML();
        $this->exporter->exportYAML('Label');

        $this->expectException(\Nine\Exceptions\CollectionExportWriteFailure::class);
        $this->exporter->exportPHPFile(__DIR__ . '/pants/', 'name');

    }

}
