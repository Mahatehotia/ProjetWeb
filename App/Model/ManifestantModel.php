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
            ->select('*')
            ->from('manifestant');
        return $queryBuilder->execute()->fetchAll();

    }
}