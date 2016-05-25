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



    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\ClientController::index');
        $index->get("/logout", 'App\Controller\ClientController::logout')->bind('client.logout');
        $index->get("/login", 'App\Controller\ClientController::loginForm')->bind('client.login');
        $index->get("/ficheClient", 'App\Controller\ClientController::ficheClient')->bind('client.ficheClient');
        $index->post("/login", 'App\Controller\ClientController::loginValid');
        $index->get("/info", 'App\Controller\ClientController::index');


        return $index;
    }
}