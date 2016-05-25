<?php
/**
 * Created by PhpStorm.
 * User: pascal
 * Date: 07/04/16
 * Time: 11:22
 */

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class ClientModel
{
    private $connexionSql;
    private $session;

    public function __construct(Application $app)
    {
        $this->connexionSql = $app['db'];
        $this->session = $app['session'];

    }

    public function verifierLoginMdp($mail, $motdepasse){
        $queryBuilder = new QueryBuilder($this->connexionSql);
        $queryBuilder->select('id', 'nom', 'prenom', 'mdp', 'droits', 'email')
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

    public function getIdUser()
    {
        if ($this->session->get('logged') != null){
            return $this->session->get('id');
        }
        return null;
    }

    public function getAllFicheClient(){
        $queryBuilder = new QueryBuilder($this->connexionSql);
        $queryBuilder->select('nom', 'prenom','email')
            ->from('client');
        return $queryBuilder->execute()->fetchAll();
    }

    public function verifierDroit($id){
        $queryBuilder = new QueryBuilder($this->connexionSql);
        $queryBuilder -> select('*')
            ->from('client')
            ->where('id = :idUser')
            ->setParameter('idUser',$id);
        return $queryBuilder->execute()->fetch();
    }

    public function getFicheClient($idClient){
        $queryBuilder = new QueryBuilder($this->connexionSql);
        $queryBuilder->select('nom', 'prenom','email','droits')
            ->from('client')
            ->where('id = :idClient')
            ->setParameter('idClient',$idClient);
        return $queryBuilder->execute()->fetch();
    }

    public function estAdmin(){
        $id = $this->getIdUser();

        $queryBuilder = new QueryBuilder($this->connexionSql);
        $queryBuilder -> select('droits')
            ->from('client')
            ->where('id = :idUser and droits=:d')
            ->setParameter('idUser',$id)
            ->setParameter('d', 'admin');

        return ($queryBuilder->execute()->rowCount() == 1);
    }

}