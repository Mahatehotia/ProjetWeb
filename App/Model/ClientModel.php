<?php
/**
 * Created by PhpStorm.
 * User: pascal
 * Date: 07/04/16
 * Time: 11:22
 */

namespace App\Model;

use Silex\Application;

use Doctrine\DBAL\Query\QueryBuilder;

class ClientModel
{
    private $connexionSql;

    public function __construct(Application $app)
    {
        $this->connexionSql = $app['db'];
    }

    public function verifierLoginMdp($mail, $motdepasse){
        $queryBuilder = new QueryBuilder($this->connexionSql);
        $queryBuilder->select('nom', 'prenom', 'mdp', 'droits', 'email')
            ->from('client')
            ->where('email = :mail and mdp = :mdp')
            ->setParameter('mail', $mail)
            ->setParameter('mdp', md5($motdepasse));
        if($queryBuilder->execute()->rowCount() == 1){
            return $queryBuilder->execute()->fetch();
        }else{
            return false;
        }
    }

}