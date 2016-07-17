<?php namespace Nine\Collections;

use InvalidArgumentException;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $config;

    public function setUp()
    {
        $this->config = new Config;
        $this->config->importArray([
            'app' => [
                'files'       => 'OS::Folders',
                'views'       => [
                    'home'    => 'HomeViewController',
                    'contact' => 'ContactViewController',
                ],
                'controllers' => [
                    'login'     => 'LoginHttpController',
                    'dashboard' => 'DashboardHttpController',
                ],
            ],
        ]);

    }

    public function test_config_should_accurately_import_a_number_of_files_in_a_directory()
    {
        // import the files in the test/config/ file.
        $this->config->importFolder(__DIR__ . '/config/');

        // we should now have a key for each filename
        static::assertArrayHasKey('app', $this->config);
        static::assertArrayHasKey('db', $this->config);
        static::assertArrayHasKey('test', $this->config);

        // get the address/city from the test.php config file.
        static::assertEquals('Vancouver', $this->config['test.address.city']);

        // import JSON files in the config folder
        $test_config = new Config;
        $test_config->importFolder(__DIR__ . '/config/', '*.json');

        // get the address/city from the config.json config file.
        static::assertEquals('Vancouver', $test_config['test.address.city']);

        // import YAML files in the config folder
        $test_config = NULL;
        $test_config = new Config;
        $test_config->importFolder(__DIR__ . '/config/', '*.yml');

        // get the address/city from the config.json config file.
        static::assertEquals('Vancouver', $test_config['test.address.city']);

    }

    public function test_config_should_accurately_import_yaml_files()
    {
        // load Config
        $this->config->importFolder(__DIR__ . '/config/');

        // new working config instance
        $new_config = new Config();

        // import the yaml config file
        $new_config->importYAML(__DIR__ . '/config/config.yml');

        // the new attributes should be equal to the saved attributes
        //static::assertEquals($this->config->getItems(), $new_config->getItems());

        // fail Yaml import
        $this->expectException('InvalidArgumentException');
        $new_config->importYAML(__DIR__ . '/config/file-not-there.yml');
    }

    public function test_config_should_correctly_import_a_configuration_array()
    {
        $this->config->importArray([
            'db' => [
                'files'     => NULL,
                'models'    => [
                    'home'    => 'HomeModel',
                    'contact' => 'ContactModel',
                ],
                'composers' => [
                    'home'    => 'HomeModelComposer',
                    'contact' => 'ContactModelComposer',
                ],
            ],
        ]);

        static::assertArrayHasKey('app', $this->config, 'config `app` key must still exist.');
        static::assertArrayHasKey('db', $this->config, 'config `db` key must now exist.');
    }

    public function test_config_should_correctly_instantiate_with_configuration_files()
    {
        //echo "\n+config";

        static::assertNotNull($this->config, '$this->config must not be NULL.');
        static::assertInstanceOf(Config::class, $this->config, '$this->config must be instance of Config.');

        $config = Config::createFromYaml(__DIR__ . '/config/config.yml');
        static::assertInstanceOf(Config::class, $config, 'createFromYaml must return an instance of Config');

        $config = Config::createFromFolder(__DIR__ . '/config');
        static::assertInstanceOf(Config::class, $config, 'createFromFolder must return an instance of Config');
    }

    public function test_config_should_provide_varying_methods_to_access_config_data()
    {
        // get()
        static::assertEquals(['home' => 'HomeViewController', 'contact' => 'ContactViewController',],
            $this->config->{'app.views'},
            'config app.views must contain the provided array.'
        );

        // array access
        static::assertEquals(['login' => 'LoginHttpController', 'dashboard' => 'DashboardHttpController',],
            $this->config['app.controllers'],
            'config app.controllers must contain the provided array.'
        );

        // alternate array
        static::assertEquals('OS::Folders',
            $this->config->{'app.files'},
            'config app.files must contain `OS::Folders`.'
        );
    }

    public function test_import_export_compile()
    {
        // load Config as usual
        $this->config->importFolder(__DIR__ . '/config/');
        // save a copy of it fr later
        $expected = $this->config->copy();

        // render the config as a YAML string
        $yaml = $this->config->exportYAML();
        // then import it again
        $this->config->importYAML($yaml);
        // the config data should remain identical
        static::assertEquals($expected, $this->config->copy());

        // now we'll try depending on a base path
        $this->config->setBasePath(__DIR__ . '/');
        $this->config->setCompilePath(__DIR__ . '/../');
        // this should compile to the basePath + Config::COMPILED_CONFIG_FILENAME
        $this->config->compile();
        // re-import it and clean-up
        $this->config->importFolder('');
        $this->config->importCompiledFile();

        // clean-up
        unlink(__DIR__ . '/../' . Config::COMPILED_CONFIG_FILENAME);
        // validate that the imported compiled config bears the 'compiled' tag
        static::assertArrayHasKey('compiled', $this->config);

        // forget the compiled tag
        $this->config->forget('compiled');
        // export the config as a JSON string
        //$json = $this->config->exportFormattedJSON();
        $json = $this->config->exportJSON();
        // save it for testing
        file_put_contents(__DIR__ . '/pushed.json', $json);
        // re-import it
        $this->config->importJSON(__DIR__ . '/pushed.json');
        // create a new config object with the newly-exported jason
        $config = Config::createFromJson(file_get_contents(__DIR__ . '/pushed.json'));
        // clean-up
        unlink(__DIR__ . '/pushed.json');
        // the two config copies should have identical contents
        static::assertEquals($expected, $config->copy());

        // finally, verify that an exception is thrown when an invalid basePath is set.
        $this->expectException(InvalidArgumentException::class);
        $config->setBasePath('somewhere-in-space');
    }

    public function test_import_file()
    {
        $config = new Config;
        $config->importFile(__DIR__.'/config/app.php');
    }

    public function test_bad_import_by_extension_extension()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->config->importByExtension('.glob','');
    }

    public function test_bad_import_by_extension_path()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->config->importByExtension('.php','/blah ');
    }

}
