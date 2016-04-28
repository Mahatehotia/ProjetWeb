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
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class CommandeController implements ControllerProviderInterface
{
    private $commandeModel;
    private $clientModel;

    public function listCommandesClient(){

    }

    public function listCommandesAdmin(){

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

        $index->get('/valide', 'App\Controller\CommandeController::validCommandeClient')->bind('commande.valider');
        
        return $index;
    }
}