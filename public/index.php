<?php
require __DIR__ .  '/../vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;




$kernel = new Kernel();

$dotenv = new Dotenv();
$dotenv->loadEnv(dirname(__DIR__).'/.env');

date_default_timezone_set($_ENV['TIME_ZONE']);

if (php_sapi_name() === 'cli') {
    $application = new Application();
    foreach ($kernel->getContainer()->findTaggedServiceIds('console.command') as $id => $tags) {
        $application->add($kernel->getContainer()->get($id));
    }
    try {
        exit($application->run());
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
