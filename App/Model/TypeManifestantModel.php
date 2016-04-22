<?php
/**
 * Created by PhpStorm.
 * User: pascal
 * Date: 22/04/16
 * Time: 18:18
 */

namespace App\Model;


use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;

class TypeManifestantModel
{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllTypes(){
        $queryBuilder = new QueryBuilder($this->db);

        $queryBuilder->select('libelle', 'id')
            ->from('typeManifestants')
            ->orderBy('id');

        return $queryBuilder->execute()->fetchAll();
    }
}