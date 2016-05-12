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
    private $manifestantModel;

    public function listCommandesClient(Application $app){
        $this->commandeModel = new CommandeModel($app);
        $this->panierModel = new PanierModel($app);
        $this->clientModel = new ClientModel($app);
        $commandes= $this->commandeModel->getAllCommandesClient($this->clientModel->getIdUser());
        for($i = 0; $i < count($commandes); $i++){
            $commandes[$i]['detail']=$this->panierModel->getDetailCommande($commandes[$i]['idCommande']);;
        }
        return $app["twig"]->render('commande/v_table_commandeClient.twig',['data'=>$commandes,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }

    public function listCommandesAdmin(Application $app){
        $this->commandeModel = new CommandeModel($app);
        $this->panierModel = new PanierModel($app);
        $commandes= $this->commandeModel->getAllCommandes();
        for($i = 0; $i < count($commandes); $i++){
            $commandes[$i]['detail']=$this->panierModel->getDetailCommande($commandes[$i]['idCommande']);;
        }
        return $app["twig"]->render('commande/v_table_commandeAdmin.twig',['data'=>$commandes,'path'=>BASE_URL,'_SESSION'=>$_SESSION]);
    }

    public function validCommandeClient(Application $app){
        $this->clientModel = new ClientModel($app);
        $idClient = $this->clientModel->getIdUser();
        $this->commandeModel = new CommandeModel($app);

        $this->commandeModel->createCommande($idClient);

        return $app->redirect($app["url_generator"]->generate('manifestant.show'));
    }

    public function connect(Application $app)
    {   
        $index = $app['controllers_factory'];

        $index->get('/showAdmin','App\Controller\CommandeController::listCommandesAdmin')->bind('commande.adminList');

        $index->get('/showClient', 'App\Controller\CommandeController::listCommandesClient')->bind('commande.clienList');


        $index->get('/valide', 'App\Controller\CommandeController::validCommandeClient')->bind('commande.valider');
        
        return $index;
    }
}