<?php
/**
 * Created by PhpStorm.
 * User: pascal
 * Date: 07/04/16
 * Time: 11:25
 */

namespace App\Controller;



use App\Model\ClientModel;
use App\Model\CommandeModel;
use App\Model\PanierModel;
use Silex\Application;
use Silex\ControllerProviderInterface;

class ClientController implements ControllerProviderInterface
{


    private $clientModel;
    private $commandeModel;
    private $panierModel;

    public function loginForm(Application $app){
        return $app['twig']->render('client/v_login_form.twig', array('path'=>BASE_URL));
    }

    public function loginValid(Application $app){
        $user = $_POST['email'];
        $pass = $_POST['password'];

        $modelClient = new ClientModel($app);

        $donnees = $modelClient->verifierLoginMdp($user, $pass);
        if($donnees == false){
            $app['session']->getFlashBag()->add('error', 'Identifiants invalides !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }else{
            $app['session']->clear();
            $app['session']->getFlashBag()->add('success', "Connexion réussie !");

            $app['session']->set('logged', 1);
            $app['session']->set('droit', $donnees['droits']);
            $app['session']->set('user', $donnees['nom']);
            $app['session']->set('id', $donnees['id']);

            return $app->redirect($app["url_generator"]->generate('manifestant.show'));
        }
    }

    public function ficheClient(Application $app){
        $this->clientModel = new ClientModel($app);
        if(!$this->clientModel->estClient()){
            $app['session']->getFlashBag()->add('msg', 'Veuillez vous identifier !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }
        $this->clientModel = new ClientModel($app);
        $idClient=$this->clientModel->getIdUser();
        $this->panierModel = new PanierModel($app);
        if ($idClient==null) {
            return $app['twig']->render('client/v_login_form.twig', array('path'=>BASE_URL));
        }
        $this->commandeModel = new CommandeModel($app);
        $commandes= $this->commandeModel->getAllCommandesClient($idClient);
        $donnees = $this->clientModel->getFicheClient($idClient);

        for($i = 0; $i < count($commandes); $i++){
            $commandes[$i]['detail']=$this->panierModel->getDetailCommande($commandes[$i]['idCommande']);;
        }
            return $app["twig"]->render('client/v_ficheClient.twig',['donnees'=>$donnees,'commandes'=>$commandes,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
        }

    public function logout(Application $app){
        $app['session']->clear();
        $app['session']->getFlashBag()->add('msg', 'Vous êtes déconnecté');
        return $app->redirect($app["url_generator"]->generate('manifestant.show'));
    }

    public function ficheAllClient(Application $app){
        $this->clientModel = new ClientModel($app);

        if(!$this->clientModel->estAdmin()) {
            $app['session']->getFlashBag()->add('error', 'Droits insufisants !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }

        $this -> clientModel = new ClientModel($app);
        $idClient=$this->clientModel->getIdUser();
        $droits = $this->clientModel->getFicheClient($idClient);
        if ($droits.['droits']=='user')
            return $app['twig']->redirect($app["url_generator"]->generate('manifestant.show'));

        $allClients=$this->clientModel->getAllFicheClient();
        return $app["twig"]->render('client/v_ficheAllClient.twig',['droits'=>$droits,'clients'=>$allClients,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }

    public function updateClient(Application $app){
        $this->clientModel = new ClientModel($app);
        if(!$this->clientModel->estClient()){
            $app['session']->getFlashBag()->add('msg', 'Veuillez vous identifier !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }
        $this->clientModel = new ClientModel($app);
        $id = $this->clientModel->getIdUser();
        $donnees = $this ->clientModel->getFicheClient($id);
        $this->commandeModel = new CommandeModel($app);
        $this->panierModel = new PanierModel($app);
        $commandes= $this->commandeModel->getAllCommandesClient($id);
        for($i = 0; $i < count($commandes); $i++){
            $commandes[$i]['detail']=$this->panierModel->getDetailCommande($commandes[$i]['idCommande']);;
        }
        return $app["twig"]->render("client/v_formUpdateFicheClient.twig",['donnees'=>$donnees,'commandes'=>$commandes,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);

    }

    public function validFormUpdateClient(Application $app)
    {
        $this->clientModel = new ClientModel($app);
        if(!$this->clientModel->estClient()){
            $app['session']->getFlashBag()->add('msg', 'Veuillez vous identifier !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }
        $erreurs = array();
        $donnees = array();

        $this->commandeModel = new CommandeModel($app);
        $this->clientModel = new ClientModel($app);
        $id = $this->clientModel->getIdUser();

        $donnees['id'] = $id;
        $donnees['nom'] = htmlspecialchars($_POST['nom']);
        $donnees['prenom'] = htmlspecialchars($_POST['prenom']);
        $donnees['email'] = htmlspecialchars($_POST['email']);
        if (!empty($erreurs)) {
            $this->clientModel = new ClientModel($app);
            $donnees = $this ->clientModel->getFicheClient($id);
            $commandes= $this->commandeModel->getAllCommandesClient($id);
            return $app["twig"]->render("client/v_formUpdateFicheClient.twig", array('erreurs' => $erreurs, 'commandes' => $commandes, 'donnees' => $donnees, 'path' => BASE_URL, '_SESSION' => $_SESSION));
        }else {
            $this->clientModel = new ClientModel($app);
            $this->clientModel->updateFicheClient($id, $donnees);
            return $app->redirect($app["url_generator"]->generate('client.ficheClient'));
        }
    }

    public function inscriptionUsers(Application $app){
        $this->clientModel = new ClientModel($app);
        return $app["twig"]->render("client/v_inscriptionUsers.twig",['path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }

    public function validInscriptionUsers(Application $app){
        $erreurs = array();
        $donnees=array();

        $donnees['nom']=htmlspecialchars($_POST['nom']);
        $donnees['prenom']=htmlspecialchars($_POST['prenom']);
        $donnees['email']=htmlspecialchars($_POST['email']);
        $donnees['mdp']=htmlspecialchars($_POST['mdp']);
        $donnees['mdp2']=htmlspecialchars($_POST['mdp2']);


        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='Manque votre nom';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['prenom']))) $erreurs['prenom']='Manque votre prenom';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['email']))) $erreurs['email']='Manque votre email';
        if ((! preg_match("/^[A-Za-z ]{4,}/",$donnees['mdp']))) $erreurs['mdp']='Votre doit composer 4 caractères';
        if ($donnees['mdp']!=$donnees['mdp2']) $erreurs['mdp2']='Pas de correspondance entre les 2 mots de passe';

        if(! empty($erreurs)){
            $this->clientModel=new ClientModel($app);
            return $app["twig"]->render('client/v_inscriptionUsers.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
        }
        else {

            $this->clientModel=new ClientModel($app);
            $this->clientModel->inscription($donnees['nom'],$donnees['prenom'],$donnees['email'],$donnees['mdp2']);
            return $app->redirect($app["url_generator"]->generate("manifestant.show"));

        }
    }



    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\ClientController::index');
        $index->get("/logout", 'App\Controller\ClientController::logout')->bind('client.logout');
        $index->get("/login", 'App\Controller\ClientController::loginForm')->bind('client.login');
        $index->get("/ficheClient", 'App\Controller\ClientController::ficheClient')->bind('client.ficheClient');
        $index->get("/ficheAllClient", 'App\Controller\ClientController::ficheAllClient')->bind('client.ficheAllClient');
        $index->post("/login", 'App\Controller\ClientController::loginValid');
        $index->get("/info", 'App\Controller\ClientController::index');

        $index->get('/edit', 'App\Controller\ClientController::updateClient')->bind('updateClient.edit');
        $index->post('/edit', 'App\Controller\ClientController::validFormUpdateClient');

        $index->get('/inscription','App\Controller\ClientController::inscriptionUsers')->bind('client.inscriptionUsers');
        $index->post('/inscription','App\Controller\ClientController::validInscriptionUsers')->bind('client.validInscriptionUsers');

        return $index;
    }
}