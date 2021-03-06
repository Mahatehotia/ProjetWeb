<?php
/**
 * Created by PhpStorm.
 * User: pascal
 * Date: 28/04/16
 * Time: 09:29
 */

namespace App\Controller;


use App\Model\ClientModel;
use App\Model\CommandeModel;
use App\Model\PanierModel;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class CommandeController implements ControllerProviderInterface
{
    private $commandeModel;
    private $clientModel;
    private $panierModel;

    public function listCommandesClient(Application $app){
        $this->clientModel = new ClientModel($app);
        if(!$this->clientModel->estClient()){
            $app['session']->getFlashBag()->add('msg', 'Veuillez vous identifier !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }
        $this->commandeModel = new CommandeModel($app);
        $this->panierModel = new PanierModel($app);
        $this->clientModel = new ClientModel($app);
        $commandes= $this->commandeModel->getAllCommandesClient($this->clientModel->getIdUser());
        for($i = 0; $i < count($commandes); $i++){
            $commandes[$i]['detail']=$this->panierModel->getDetailCommande($commandes[$i]['idCommande']);
        }
        return $app["twig"]->render('commande/v_table_commandeClient.twig',['data'=>$commandes,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }

    public function listCommandesAdmin(Application $app){
        $this->clientModel = new ClientModel($app);
        if($this->clientModel->estAdmin()){
            $this->commandeModel = new CommandeModel($app);
            $this->panierModel = new PanierModel($app);
            $commandes = $this->commandeModel->getAllCommandes();
            for ($i = 0; $i < count($commandes); $i++) {
                $commandes[$i]['detail'] = $this->panierModel->getDetailCommande($commandes[$i]['idCommande']);;
            }
            return $app["twig"]->render('commande/v_table_commandeAdmin.twig', ['data' => $commandes, 'path' => BASE_URL, '_SESSION' => $_SESSION]);
        }
        $app['session']->getFlashBag()->add('error', 'Droits insufisants !');
        return $app->redirect($app["url_generator"]->generate('client.login'));
    }

    public function createCommandeClient(Application $app){
        $this->clientModel = new ClientModel($app);
        if(!$this->clientModel->estClient()){
            $app['session']->getFlashBag()->add('msg', 'Veuillez vous identifier !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }

        $this->clientModel = new ClientModel($app);
        $idClient = $this->clientModel->getIdUser();
        $this->commandeModel = new CommandeModel($app);

        $this->commandeModel->createCommande($idClient);

        //Mail confirmant la création d'une commande
        $to      = $this->clientModel->getFicheClient($idClient)['email'];
        $subject = 'Votre commande a bien été passée';
        $message = 'Nous vous informons de la bonne réception de votre commande, elle sera traitée dans les plus brefs délais.';
        $headers = 'From: vendeur@manifestons.com' . "\r\n" .
            'Reply-To: vendeur@manifestons.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);

        return $app->redirect($app["url_generator"]->generate('manifestant.show'));
    }
    
    public function validCommandeClient(Application $app){
        $this->commandeModel = new CommandeModel($app);
        $this->clientModel = new ClientModel($app);

        if(!$this->clientModel->estAdmin()) {
            $app['session']->getFlashBag()->add('error', 'Droits insufisants !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }

        if($this->clientModel->estAdmin() &&  isset($_POST['idCommande']) && is_numeric($_POST['idCommande']) && isset($_POST['idClient']) && is_numeric($_POST['idClient'])){
            $this->commandeModel->validerCommande($_POST['idClient'], $_POST['idCommande']);
        }

        return $app->redirect($app["url_generator"]->generate('commande.adminList'));
    }

    public function annulerCommandeClient(Application $app){
        $this->commandeModel = new CommandeModel($app);
        $this->clientModel = new ClientModel($app);

        if(!$this->clientModel->estAdmin()) {
            $app['session']->getFlashBag()->add('error', 'Droits insufisants !');
            return $app->redirect($app["url_generator"]->generate('client.login'));
        }

        if($this->clientModel->estAdmin() &&  isset($_POST['idCommande']) && is_numeric($_POST['idCommande']) && isset($_POST['idClient']) && is_numeric($_POST['idClient'])){
            $this->commandeModel->annulerCommande($_POST['idClient'], $_POST['idCommande']);
        }

        return $app->redirect($app["url_generator"]->generate('commande.adminList'));
    }

    public function connect(Application $app)
    {   
        $index = $app['controllers_factory'];

        $index->get('/showAdmin','App\Controller\CommandeController::listCommandesAdmin')->bind('commande.adminList');

        $index->get('/showClient', 'App\Controller\CommandeController::listCommandesClient')->bind('commande.clientList');

        $index->get('/valide', 'App\Controller\CommandeController::createCommandeClient')->bind('commande.creer');

        //Administration d'une commande
        $index->put('/validerCommande', 'App\Controller\CommandeController::validCommandeClient')->bind('commande.valider');
        $index->put('/annulerCommande', 'App\Controller\CommandeController::annulerCommandeClient')->bind('commande.annuler');

        return $index;
    }
}