<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 07/04/16
 * Time: 10:05
 */

namespace App\Controller;

use App\Model\ManifestantModel;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

use App\Model\PanierModel;
use App\Model\ClientModel;

class PanierController implements ControllerProviderInterface{

    private $panierModel;
    private $clientModel;
    private $manifestantModel;

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



    public function addArticle(Application $app){
        $idManifestant = $_POST['idManifestant'];
        $quantite = $_POST['quantite'];
        $this ->panierModel = new PanierModel($app);
        $this ->clientModel = new ClientModel($app);
        $this ->manifestantModel = new ManifestantModel($app);
        $id = $this ->clientModel ->getIdUser();
        if($this->panierModel->getNombreInPanier($id, $idManifestant) > 0){
            echo "Incrementation";
            $this->panierModel->incArticleClient($id, $idManifestant, $quantite);
        }else{
            echo "Initialisation";
            $this->panierModel->addArticleClient($id,$idManifestant,$quantite);
        }
        
        return $app->redirect($app["url_generator"]->generate('manifestant.show'));
    }

    public function removeArticle(Application $app){
        $idManifestant = $idManifestant = $_POST['idManifestant'];
        $this ->panierModel = new PanierModel($app);
        $this ->clientModel = new ClientModel($app);
        $id = $this ->clientModel ->getIdUser();

        if ($nb = $this->panierModel->getNombreInPanier($id, $idManifestant) > 1){
            $this->panierModel->descArticleClient($id, $idManifestant, 1);
        }else{
            $this->panierModel->deleteArticleClient($id, $idManifestant);
        }

        return $app->redirect($app["url_generator"]->generate('panier.show'));
    }

    public function removeArticlePanier(Application $app){
        $idManifestant = $idManifestant = $_POST['idManifestant'];
        $this ->panierModel = new PanierModel($app);
        $this ->clientModel = new ClientModel($app);
        $id = $this ->clientModel ->getIdUser();

        if ($nb = $this->panierModel->getNombreInPanier($id, $idManifestant) > 1){
            $this->panierModel->descArticleClient($id, $idManifestant, 1);
        }else{
            $this->panierModel->deleteArticleClient($id, $idManifestant);
        }

        return $app->redirect($app["url_generator"]->generate('manifestant.show'));
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\PanierController::show')->bind('panier.show');
        $index->post("/validerPanier", 'App\Controller\PanierController::panierValide')->bind('panier.valide');
        $index->post("/ajouterManifestant", 'App\Controller\PanierController::addArticle')->bind('panier.ajout');
        $index->post("/enleverManifestant", 'App\Controller\PanierController::removeArticle')->bind('panier.remove');
        $index->post("/enleverManifestantPanier", 'App\Controller\PanierController::removeArticlePanier')->bind('panier2.remove');
        return $index;
    }
}