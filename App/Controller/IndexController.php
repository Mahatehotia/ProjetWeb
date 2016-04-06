<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 03/03/16
 * Time: 09:23
 */

namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;


class IndexController implements ControllerProviderInterface
{
    public function index(Application $app)
    {
        return $app["twig"]->render("v_layout.twig", ['path' => BASE_URL, '_SESSION' => $_SESSION]);
    }
    public function connect(Application $app)
    {
        // créer un nouveau controleur basé sur la route par défaut
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\IndexController::index');
        $index->match("/index", 'App\Controller\IndexController::index');

        return $index;
    }



}
