<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 07/04/16
 * Time: 00:21
 */
namespace App\Controller;

use App\Model\ClientModel;
use App\Model\PanierModel;
use App\Model\TypeManifestantModel;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

use App\Model\ManifestantModel;
use Symfony\Component\Validator\Constraints\Regex;


class ManifestantController implements ControllerProviderInterface{

    private $manifestantModel;
    private $typeManifestantModel;

    public function __construct()
    {
    }

    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app) {
        $this->manifestantModel = new ManifestantModel($app);
        $manifestant = $this->manifestantModel->getAllManifestant();
        $panierModel = new PanierModel($app);
        $clientModel = new ClientModel($app);
        $panier = $panierModel->getPanierClient($clientModel->getIdUser());
        return $app["twig"]->render('manifestant/v_table_manifestant.twig',['data'=>$manifestant, 'panier' => $panier, 'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }
    public function add(Application $app){
        $this->typeManifestantModel = new TypeManifestantModel($app);
        $types = $this->typeManifestantModel->getAllTypes();
        return $app["twig"]->render('manifestant/v_form_create_manifestant.twig',['types'=>$types,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }
    public function validAdd(Application $app){
        $erreurs=array();
        $donnees['typeManifestant']=htmlspecialchars($_POST['typeManifestant']);
        $donnees['nomManifestant']=htmlspecialchars($_POST['nomManifestant']);
        $donnees['descriptionManifestant']=htmlspecialchars($_POST['descriptionManifestant']);
        $donnees['prixManifestant']=htmlspecialchars($_POST['prixManifestant']);
        $donnees['imageManifestant']=htmlspecialchars($_POST['imageManifestant']);
        $donnees['quantiteManifestant']=htmlspecialchars($_POST['quantiteManifestant']);

        if(! is_numeric($donnees['typeManifestant']))$erreurs['typeManifestant']='sélectionner un Type de Manifestant';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nomManifestant']))) $erreurs['nomManifestant']='il manque un nom';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['descriptionManifestant']))) $erreurs['descriptionManifestant']='il manque une description';
        if(!is_numeric($donnees['prixManifestant']) and ($donnees['prixManifestant'])<0)$erreurs['prixManifestant']='saisir un montant';
        if ((! preg_match("/jpg|png|jpeg/",$donnees['imageManifestant']))) $erreurs['imageManifestant']='choisir une image format (.jpg, .png ou .jpeg)';
        if(! is_numeric($donnees['quantiteManifestant']))$erreurs['quantiteManifestant']='saisir un stock';
        if(! empty($erreurs))
        {
            $this->typeManifestantModel = new TypeManifestantModel($app);
            $types = $this->typeManifestantModel->getAllTypes();
            $this->manifestantModel = new ManifestantModel($app);
            $manifestant=$this->manifestantModel->getAllManifestant();
            return $app["twig"]->render('manifestant/v_form_create_manifestant.twig',['types'=>$types,'donnees'=>$donnees,'erreurs'=>$erreurs,'manifestant' => $manifestant,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
        }
        else {

            $this->manifestantModel = new ManifestantModel($app);
            $this->manifestantModel->ajouterManifestant($donnees);
            return $app->redirect($app["url_generator"]->generate("manifestant.index"));

        }
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
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\ManifestantController::index')->bind('manifestant.index');

        $index->get("/show", 'App\Controller\ManifestantController::show')->bind('manifestant.show');

        $index->get("/add", 'App\Controller\ManifestantController::add')->bind('manifestant.add');
        $index->post("/add", 'App\Controller\ManifestantController::validAdd');

        $index->get('/delete/{id}', 'App\Controller\ManifestantController::deleteManifestant')->bind('manifestant.delete');

        $index->get('/edit/{id}', 'App\Controller\ManifestantController::edit')->bind('manifestant.edit');

        return $index;
    }
}