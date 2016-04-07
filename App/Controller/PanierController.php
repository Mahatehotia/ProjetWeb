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
use App\Model\UserModel;

class PanierController implements ControllerProviderInterface{

    private $panierModel;

    public function __construct(){
    }

    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app) {
        $this->panierModel = new PanierModel($app);
        $this->userModel = new UserModel($app);
        $id= $this -> userModel -> getIdUser($app);
        $panier = $this->panierModel->getPanierClient($id);
        return $app["twig"]->render('panier/v_table_panier.twig',['data'=>$panier,'path'=>BASE_URL,'_SESSION'=>$_SESSION],'user'=>$id);
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
    }
}