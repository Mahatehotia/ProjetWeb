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

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getPanierClient($idClient){
        if ($idClient == null)
            return null;
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('panier.quantite', 'm.nom', 'm.prix')
            ->from('panier')->leftJoin('panier', 'manifestant', 'm', 'm.id=panier.idManifestant')
            ->where('panier.idClient=:id')
            ->setParameter('id', $idClient);
        return $queryBuilder->execute()->fetchAll();
    }

    public function addArticleClient($idClient,$idManifestant,$quantite){
        if ($idClient == null) return null;

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
            ->where('panier.idClient=:id' and 'panier.idManifestant=:idManisfestant')
            ->setParameter('id', $idClient)
            ->setParameter('idManisfestant',$idManifestant)
        ;
        return $queryBuilder->execute();
    }
    public function deleteArticleClient($idClient,$idManifestant){}

    public function incArticleClient($idClient,$idManifestant,$quantite){
            $queryBuilder = new QueryBuilder($this->db);
            $queryBuilder
                ->insert('panier')
                ->values([
                    'idClient'=> '?',
                    'quantite'=> '?',
                    'idManifestant'=> '?'
                ])
                ->setParameter(0,$idClient)
                ->setParameter(1,$this->getNombreInPanier($idClient,$idManifestant)+$quantite)
                ->setParameter(2,$idManifestant)
                ;
            return $queryBuilder->execute();
    }

    public function descArticleClient($idClient,$idManifestant,$quantite){}
}