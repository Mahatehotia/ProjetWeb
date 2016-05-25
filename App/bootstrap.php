<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 03/03/16
 * Time: 09:04
 */

include('config.php');

session_start();

ini_set('date.timezone', 'Europe/Paris');

use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../vendor/autoload.php';
$loader->add("App",dirname(__DIR__));
$loader->addPsr4('App\\',__DIR__);

$app = new Silex\Application();
$app['debug'] = true;

Request::enableHttpMethodParameterOverride();

//Base de données
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbhost' => hostname,
        'dbname' => database,
        'user' => username,
        'password' => password,
        'charset'   => 'utf8mb4',
    ),
));


//Sessions, url, silex
$app->register(new Silex\Provider\SessionServiceProvider());

$app -> register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => join(DIRECTORY_SEPARATOR, array(__DIR__, 'View'))
));

$app->mount("/", new App\Controller\IndexController());
$app->mount("/manifestant", new App\Controller\ManifestantController());
$app->mount('/panier', new App\Controller\PanierController());
$app->mount('/client', new App\Controller\ClientController());
$app->mount('/commande', new App\Controller\CommandeController());



$app->run();