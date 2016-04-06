<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 07/04/16
 * Time: 00:22
 */

class PartisansModel{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllPartisans() {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id','p.typePartisans_id','p.nom','p.prix','p.quantites','p.photo','p.stock')
            ->from('partisans', 'p')
            ->addOrderBy('p.id');
        return $queryBuilder->execute()->fetchAll();
    }

    public function deleteUnPartisans($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->delete('partisans')
            ->where('id = :id')
            ->setParameter('id',$id);
        return $queryBuilder->execute();
    }
}