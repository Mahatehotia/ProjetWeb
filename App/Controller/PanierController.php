<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 07/04/16
 * Time: 10:05
 */

namespace App\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

use App\Model\PanierModel;
use App\Model\ClientModel;

class PanierController implements ControllerProviderInterface{

    private $panierModel;
    private $clientModel;

    public function __construct(){
    }

    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app) {
        $this->panierModel = new PanierModel($app);
        $this->clientModel = new ClientModel($app);
        $id = $this->clientModel->getIdUser();
        $panier = $this->panierModel->getPanierClient($id);
        return $app["twig"]->render('panier/v_table_panier.twig',['panier'=>$panier,'path'=>BASE_URL,'_SESSION'=>$_SESSION,'user'=>$id]);
    }


    
    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\PanierController::show')->bind('panier.show');
        return $index;
    }
}