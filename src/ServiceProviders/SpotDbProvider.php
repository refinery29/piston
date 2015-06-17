<?php namespace Refinery29\Piston\ServiceProviders;

use League\Container\ServiceProvider;
use Spot\Config;
use Spot\Locator;

/**
 * Created by PhpStorm.
 * User: kayla.daniels
 * Date: 6/15/15
 * Time: 1:40 PM
 */
class SpotDbProvider extends ServiceProvider
{
    protected $provides = [
        'db'
    ];

    public function register()
    {
        $this->validateEnvConfig();

        $cfg = new Config();
        $cfg->addConnection('mysql', [
            'dbname'   => $_ENV['DB_NAME'],
            'user'     => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'host'     => $_ENV['DB_HOST'],
            'driver'   => $_ENV['DB_DRIVER'],
        ]);
        $this->container['db'] = new Locator($cfg);;
    }

    private function validateEnvConfig()
    {
        if (!isset($_ENV['DB_NAME'])
            || !isset($_ENV['DB_USER'])
            || !isset($_ENV['DB_PASSWORD'])
            || !isset($_ENV['DB_HOST'])
            || !isset($_ENV['DB_DRIVER'])
            ){
            throw new \Exception();
        }
    }
}