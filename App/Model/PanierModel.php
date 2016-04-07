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
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('panier.quantite', 'm.nom', 'm.prix')
            ->from('panier')->leftJoin('panier', 'manifestant', 'm', 'm.id=panier.idManifestant');
        return $queryBuilder->execute()->fetchAll();
    }
}