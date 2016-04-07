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

use App\Model\ManifestantModel;


class ManifestantController implements ControllerProviderInterface{

    private $manifestantModel;

    public function __construct()
    {
    }

    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app) {
        $this->manifestantModel = new ManifestantModel($app);
        $manifestant = $this->manifestantModel->getAllManifestant();
        return $app["twig"]->render('manifestant/v_table_manifestant.twig',['data'=>$manifestant,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }
    public function add(Application $app){
        $this->manifestantModel = new ManifestantModel($app);
        $manifestant = $this->manifestantModel->getAllManifestant();
        return $app["twig"]->render('manifestant/v_form_create_manifestant.twig',['manifestant'=>$manifestant,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
        return "add Manifestant";
    }

    public function deleteManifestant(Application $app, $id)
    {
        $this->manifestantModel = new ManifestantModel($app);
        $manifestant = $this->manifestantModel->readUnManifestant($id);
        return "delete Manifestant";
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
        $index->match("/", 'App\Controller\ManifestantController::index')->bind('manifestant.index');

        $index->get("/show", 'App\Controller\ManifestantController::show')->bind('manifestant.show');

        $index->get("/add", 'App\Controller\ManifestantController::add')->bind('manifestant.add');

        $index->get('/delete/{id}', 'App\Controller\ManifestantController::deleteManifestant')->bind('manifestant.delete');

        $index->get('/edit/{id}', 'App\Controller\ManifestantController::edit')->bind('manifestant.edit');

        return $index;
    }
}