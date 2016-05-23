<?php
/**
 * Created by PhpStorm.
 * User: pascal
 * Date: 07/04/16
 * Time: 09:25
 */
namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class PanierModel{
    private $db;
    private $manifestantModel;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
        $this->manifestantModel = new ManifestantModel($app);
    }

    public function getPanierClient($idClient){
        if ($idClient == null)
            return null;
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('panier.quantite', 'm.nom', 'm.prix', 'm.id')
            ->from('panier')->leftJoin('panier', 'manifestant', 'm', 'm.id=panier.idManifestant')
            ->where('panier.idClient=:id and idCommande=-1')
            ->setParameter('id', $idClient);
        return $queryBuilder->execute()->fetchAll();
    }

    public function getDetailCommande($idCommande){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('panier.quantite', 'm.nom', 'm.prix', 'm.id')
            ->from('panier')->leftJoin('panier', 'manifestant', 'm', 'm.id=panier.idManifestant')
            ->where('idCommande=:id')
            ->setParameter('id', $idCommande);
        return $queryBuilder->execute()->fetchAll();
    }

    public function addArticleClient($idClient,$idManifestant,$quantite){
        if ($idClient == null) return null;

        if(!$this->manifestantModel->isMoreThan($idManifestant, $quantite))
            return false;

        $this->manifestantModel->prendreManifestant($idManifestant, $quantite);

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->insert('panier')
            ->values([
                'idClient'=> '?',
                'quantite'=> '?',
                'idManifestant'=> '?'
            ])
            ->setParameter(0,$idClient)
            ->setParameter(1,$quantite)
            ->setParameter(2,$idManifestant)
        ;
        return $queryBuilder->execute();
    }

    public function getNombreInPanier($idClient,$idManifestant){
        if ($idClient==null) return null;

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            -> select('quantite')
            ->from('panier')
            ->where('panier.idClient=:id and panier.idManifestant=:idManisfestant and idCommande=-1')
            ->setParameter('id', $idClient)
            ->setParameter('idManisfestant',$idManifestant)
        ;
        return $queryBuilder->execute()->fetch()['quantite'];
    }
    public function deleteArticleClient($idClient,$idManifestant){

        $this->manifestantModel->rendreManifestant($idManifestant, $this->getNombreInPanier($idClient, $idManifestant));

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            -> delete('panier')
            ->where('panier.idClient=:id and panier.idManifestant=:idManisfestant and idCommande=-1')
            ->setParameter('id', $idClient)
            ->setParameter('idManisfestant',$idManifestant)
        ;
        return $queryBuilder->execute();
    }

    public function incArticleClient($idClient,$idManifestant,$quantite){
        if(!$this->manifestantModel->isMoreThan($idManifestant, $quantite))
            return false;

        $this->manifestantModel->prendreManifestant($idManifestant, $quantite);

            $queryBuilder = new QueryBuilder($this->db);
            $queryBuilder
                ->update('panier')
                ->set('quantite', ':quantite')
                ->where('idClient = :idClient and idManifestant=:idManifestant and idCommande=-1')
                ->setParameter('idClient',$idClient)
                ->setParameter('quantite',$this->getNombreInPanier($idClient,$idManifestant)+$quantite)
                ->setParameter('idManifestant',$idManifestant)
                ;
            return $queryBuilder->execute();
    }

    public function descArticleClient($idClient,$idManifestant,$quantite){

        $this->manifestantModel->rendreManifestant($idManifestant, $quantite);

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('panier')
            ->set('quantite', ':quantite')
            ->where('idClient = :idClient and idManifestant=:idManifestant and idCommande=-1')
            ->setParameter('idClient',$idClient)
            ->setParameter('idManifestant',$idManifestant)
            ->setParameter('quantite',$this->getNombreInPanier($idClient,$idManifestant)-$quantite)
        ;
        return $queryBuilder->execute();
    }

    public function annulerPanier($idClient){
        //Récuperer le panier du client, rendre chaque élément (stock)
        
        $queryBuilder = new QueryBuilder($this->db);
        //supprimer toutes les lignes avec idclient et commande = -1

        //TODO : Finit ça pascal

    }
}