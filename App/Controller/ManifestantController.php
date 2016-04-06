<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 07/04/16
 * Time: 00:21
 */
namespace App\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class ManifestantController implements ControllerProviderInterface{

    private $manifestantModel;

    public function __construct(){
    }

    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app) {
        $this->manifestantModel = new ManifestationModel($app);
        $manifestations = $this->manifestationModel->getAllManifestant();
        return $app["twig"]->render('categorie/v_table_categorie.twig',['data'=>$manifestations,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        // TODO: Implement connect() method.
        $index = $app['controllers_factory'];
        $index->get("/show", 'App\Controller\ManifestantController::show')->bind('manifestant.show');
    }
}