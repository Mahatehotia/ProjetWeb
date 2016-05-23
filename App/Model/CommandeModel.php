<?php
/**
 * Created by PhpStorm.
 * User: pascal
 * Date: 28/04/16
 * Time: 09:30
 */

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class CommandeModel
{
    private $db;
    private $panierModel;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
        $this->panierModel = new PanierModel($app);
    }

    public function getAllCommandes(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('idCommande', 'idClient', 'date', 'etat', 'total')
            ->from('commande', 'c');
        return $queryBuilder->execute()->fetchAll();
    }

    public function getAllCommandesClient($idClient){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('idCommande', 'idClient', 'date', 'etat', 'total')
            ->from('commande', 'c')->where('idClient=:idClient')->setParameter('idClient', $idClient);
        return $queryBuilder->execute()->fetchAll();
    }

    public function createCommande($idClient){
        $prix = 0;
        $panier = $this->panierModel->getPanierClient($idClient);
        foreach ($panier as $manifestant){
            //echo $manifestant['quantite'].'*'.$manifestant['prix'];
            $prix+=$manifestant['quantite']*$manifestant['prix'];
        }
        //echo "Total : ".$prix;  

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('commande')
            ->values([
                'idClient' => '?',
                'etat' => '\'waiting\'',
                'total' => '?'
            ])
            ->setParameter(0, $idClient)
            ->setParameter(1, $prix);

        $queryBuilder->execute();
        $id = $this->db->lastInsertId();
        echo "Id de la commande : ".$id;

        $queryBuilder =  new QueryBuilder($this->db);
        $queryBuilder->update('panier')
            ->set('idCommande', ':id')
            ->where('idClient = :client and idCommande=-1')
            ->setParameter('client', $idClient)
            ->setParameter('id', $id);
        $queryBuilder->execute();
    }

    public function validerCommande($idClient,$idCommande){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('commande')
            ->set('etat',':etat')
            ->where('idClient =:client and idCommande = :id')
            ->setParameter('client',$idClient)
            ->setParameter('id',$idCommande)
            ->setParameter('etat','\'sold\'');
        $queryBuilder->execute();
    }

    public function envoyerCommande($idClient,$idCommande){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('commande')
            ->set('etat',':etat')
            ->where('idClient =:client and idCommande = :id')
            ->setParameter('client',$idClient)
            ->setParameter('id',$idCommande)
            ->setParameter('etat','\'send\'');
        $queryBuilder->execute();
    }
}