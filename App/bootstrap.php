<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 03/03/16
 * Time: 09:04
 */

include('config.php');

session_start();

//On initialise le timeZone
ini_set('date.timezone', 'Europe/Paris');

//On ajoute l'autoloader (compatible winwin)
$loader = require_once join(DIRECTORY_SEPARATOR,[dirname(__DIR__), 'vendor', 'autoload.php']);
//dans l'autoloader nous ajoutons notre répertoire applicatif
$loader->addPsr4('App\\',__DIR__);

//Nous instancions un objet Silex\Application
$app = new Silex\Application();
$app -> register(new Silex\Provider\UrlGeneratorServiceProvider());

// connexion à la base de données
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

// utilisation des sessoins
$app->register(new Silex\Provider\SessionServiceProvider());

//en dev, nous voulons voir les erreurs
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => join(DIRECTORY_SEPARATOR, array(__DIR__, 'View'))
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->mount("/", new App\Controller\IndexController());
$app->mount("/manifestant", new App\Controller\ManifestantController());
//On lance l'application
$app->run();
