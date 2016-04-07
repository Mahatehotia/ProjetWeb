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
            ->select('m.id','m.typeManifestant','m.nom','m.prix','m.photo','m.stock')
            ->from('manifestant', 'm')
            ->addOrderBy('m.id');
        return $queryBuilder->execute()->fetchAll();
    }

    public function readUnManifestant($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('m.id','t.typeManifestant','m.nom','m.prix','m.quantites','m.photo','m.stock')
            ->from('manifestant','m')
            ->innerJoin('m','typeManifestants', 't','m.typeManifestant=t.id')
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
}