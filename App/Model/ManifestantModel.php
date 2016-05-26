<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 07/04/16
 * Time: 00:22
 */

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class ManifestantModel{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllManifestant() {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('m.id','tM.libelle','m.nom','m.prix','m.photo','m.stock', 'm.description')
            ->from('manifestant', 'm')
            ->leftJoin('m', 'typeManifestants', 'tM', 'm.typeManifestant=tM.id')
            ->addOrderBy('m.id');
        return $queryBuilder->execute()->fetchAll();
    }

    public function getAllManifestantByType($type) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('m.id','tM.libelle','m.nom','m.prix','m.photo','m.stock', 'm.description')
            ->from('manifestant', 'm')
            ->leftJoin('m', 'typeManifestants', 'tM', 'm.typeManifestant=tM.id')
            ->where('tM.libelle = :type')
            ->setParameter('type', $type)
            ->addOrderBy('m.id');
        return $queryBuilder->execute()->fetchAll();
    }

    public function ajouterManifestant($donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->insert('manifestant')
            ->values([
                'typeManifestant' => '?',
                'nom' => '?',
                'description' => '?',
                'prix' => '?',
                'photo'=> '?',
                'stock'=> '?',
            ])
            ->setParameter(0,$donnees['typeManifestant'])
            ->setParameter(1,$donnees['nomManifestant'])
            ->setParameter(2,$donnees['descriptionManifestant'])
            ->setParameter(3,$donnees['prixManifestant'])
            ->setParameter(4,$donnees['imageManifestant'])
            ->setParameter(5,$donnees['quantiteManifestant']);
        return $queryBuilder->execute();
    }

    public function readUnManifestant($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('m.id','tM.libelle','m.nom','m.prix','m.photo','m.stock')
            ->from('manifestant','m')
            ->innerJoin('m','typeManifestants', 'tM','m.typeManifestant=tM.id')
            ->where('m.id=:id')

            ->setParameter('id',$id);
        return $queryBuilder->execute()->fetch();
    }

    public function deleteUnManifestant($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->delete('manifestant')
            ->where('id = :id')
            ->setParameter('id',$id);
        return $queryBuilder->execute();
    }

    public function prendreManifestant($id, $quantite){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('manifestant')
            ->set('stock', ':quantite')
            ->setParameter('quantite', $this->readUnManifestant($id)['stock']-$quantite)
            ->where('id = :id')
            ->setParameter('id', $id);

        $queryBuilder->execute();
    }

    public function rendreManifestant($id, $quantite){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('manifestant')
            ->set('stock', ':quantite')
            ->setParameter('quantite', $this->readUnManifestant($id)['stock']+$quantite)
            ->where('id = :id')
            ->setParameter('id', $id);

        $queryBuilder->execute();
    }

    public function isMoreThan($id, $quantite){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('stock')
            ->from('manifestant')
            ->where('id=?')
            ->setParameter(0, $id);
        $stock = $queryBuilder->execute()->fetch()['stock'];

        if($stock >= $quantite)
            return true;
        else
            return false;
    }
}