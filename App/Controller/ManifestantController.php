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
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

use App\Model\ManifestantModel;
use Symfony\Component\Validator\Constraints\Regex;


class ManifestantController implements ControllerProviderInterface{

    private $manifestantModel;
    private $typeManifestantModel;
    private $clientModel;

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
        $this->clientModel = new ClientModel($app);

        if(!$this->clientModel->estAdmin()) {
            $app['session']->getFlashBag()->add('error', 'Droits insufisants !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }

        $this->typeManifestantModel = new TypeManifestantModel($app);
        $types = $this->typeManifestantModel->getAllTypes();
        return $app["twig"]->render('manifestant/v_form_create_manifestant.twig',['types'=>$types,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }
    public function validAdd(Application $app){
        $this->clientModel = new ClientModel($app);

        if(!$this->clientModel->estAdmin()) {
            $app['session']->getFlashBag()->add('error', 'Droits insufisants !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }

        $erreurs=array();
        $donnees['typeManifestant']=htmlspecialchars($_POST['typeManifestant']);
        $donnees['nomManifestant']=htmlspecialchars($_POST['nomManifestant']);
        $donnees['descriptionManifestant']=htmlspecialchars($_POST['descriptionManifestant']);
        $donnees['prixManifestant']=htmlspecialchars($_POST['prixManifestant']);
        $donnees['imageManifestant']=htmlspecialchars($_FILES['imageManifestant']['name']);
        $donnees['quantiteManifestant']=htmlspecialchars($_POST['quantiteManifestant']);

        if(! is_numeric($donnees['typeManifestant']))$erreurs['typeManifestant']='sélectionner un Type de Manifestant';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nomManifestant']))) $erreurs['nomManifestant']='il manque un nom';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['descriptionManifestant']))) $erreurs['descriptionManifestant']='il manque une description';
        if(!is_numeric($donnees['prixManifestant']) and ($donnees['prixManifestant'])<0)$erreurs['prixManifestant']='saisir un montant';
        if (preg_match("([^\s]+(\.(?i)(jpe?g|png|gif|bmp))$)",$donnees['imageManifestant']) != 1) $erreurs['imageManifestant']='choisir une image format (.jpg, .png ou .jpeg)';
        if(! is_numeric($donnees['quantiteManifestant']))$erreurs['quantiteManifestant']='saisir un stock';
        if(! empty($erreurs))
        {
            print_r($_FILES);
            $this->typeManifestantModel = new TypeManifestantModel($app);
            $types = $this->typeManifestantModel->getAllTypes();
            $this->manifestantModel = new ManifestantModel($app);
            $manifestant=$this->manifestantModel->getAllManifestant();
            return $app["twig"]->render('manifestant/v_form_create_manifestant.twig',['types'=>$types,'donnees'=>$donnees,'erreurs'=>$erreurs,'manifestant' => $manifestant,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
        }
        else {
            if (isset ($_FILES['imageManifestant'])){
                $imagename = $_FILES['imageManifestant']['name'];
                $source = $_FILES['imageManifestant']['tmp_name'];
                $target = "images/".$imagename;

                move_uploaded_file($source, $target);

                $donnees['imageManifestant'] = $target;
            }
            //TODO : Error (fichier image vide ? :'( )


            $this->manifestantModel = new ManifestantModel($app);
            $this->manifestantModel->ajouterManifestant($donnees);
            return $app->redirect($app["url_generator"]->generate("manifestant.index"));

        }
    }

    public function deleteManifestant(Application $app, $id)
    {
        $this->clientModel = new ClientModel($app);

        if(!$this->clientModel->estAdmin()) {
            $app['session']->getFlashBag()->add('error', 'Droits insufisants !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }

        $this->manifestantModel = new ManifestantModel($app);
        $manifestant = $this->manifestantModel->readUnManifestant($id);
        return "delete Manifestant";
    }

    public function ajoutStock(Application $app){
        $this-> clientModel = new ClientModel($app);

        if(!$this->clientModel->estAdmin()) {
            $app['session']->getFlashBag()->add('error', 'Droits insufisants !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }
        $this->manifestantModel = new ManifestantModel($app);
        $manifestant = $this->manifestantModel->getAllManifestant();

        return $app["twig"]->render('manifestant/v_tableEdit_manifestant.twig',['data'=>$manifestant, 'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }

    public function validAjoutStock(Application $app,$idManifestant){
        $quantite = $_POST['quantite'];
        $this-> clientModel = new ClientModel($app);

        if(!$this->clientModel->estAdmin()) {
            $app['session']->getFlashBag()->add('error', 'Droits insufisants !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }

        $this->manifestantModel = new ManifestantModel($app);
        $this->manifestantModel->rendreManifestant($idManifestant, $quantite);

        return $app->redirect($app["url_generator"]->generate('manifestant.stock'));
    }

    
    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\ManifestantController::index')->bind('manifestant.index');

        $index->get("/show", 'App\Controller\ManifestantController::show')->bind('manifestant.show');

        $index->get("/add", 'App\Controller\ManifestantController::add')->bind('manifestant.add');
        $index->post("/add", 'App\Controller\ManifestantController::validAdd');

        $index->get('/delete/{id}', 'App\Controller\ManifestantController::deleteManifestant')->bind('manifestant.delete');

        $index->get('/edit/{id}', 'App\Controller\ManifestantController::edit')->bind('manifestant.edit');

        $index->get('/addStock','App\Controller\ManifestantController::ajoutStock')->bind('manifestant.stock');
        $index->post('/addStock/{idManifestant}','App\Controller\ManifestantController::validAjoutStock');

        return $index;
    }
}