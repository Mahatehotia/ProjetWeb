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
        $queryBuilder->select('id','nom', 'prenom','email','droits')
            ->from('client')
            ->where('id = :idClient')
            ->setParameter('idClient',$idClient);
        return $queryBuilder->execute()->fetch();
    }

    public function estAdmin(){
        $id = $this->getIdUser();

        if($id == null)
            return false;

        $queryBuilder = new QueryBuilder($this->connexionSql);
        $queryBuilder -> select('droits')
            ->from('client')
            ->where('id = :idUser and droits=:d')
            ->setParameter('idUser',$id)
            ->setParameter('d', 'admin');

        return ($queryBuilder->execute()->rowCount() == 1);
    }

    public function estClient(){

        return ($this->getIdUser() != null);
    }

    public function updateFicheClient($idClient,$donnees){
        $queryBuilder = new QueryBuilder($this->connexionSql);
        $queryBuilder->update('client')
            ->set('email','?')
            ->set('mdp','?')
            ->set('nom','?')
            ->set('prenom','?')
            ->where('id= ?')
            ->setParameter(0, $donnees['email'])
            ->setParameter(1, $donnees['mdp'])
            ->setParameter(2, $donnees['nom'])
            ->setParameter(3, $donnees['prenom'])
            ->setParameter(4, $idClient);

        $queryBuilder->execute();
    }

    public function inscription($nom, $prenom, $email, $mdp, $droits='user'){
        $queryBuilder = new QueryBuilder($this->connexionSql);
        $queryBuilder->insert('client')
            ->values([
                'nom' => '?',
                'prenom' => '?',
                'email' => '?',
                'mdp' => 'MD5(?)',
                'droits' => '?'
            ])
            ->setParameter(0, $nom)
            ->setParameter(1, $prenom)
            ->setParameter(2, $email)
            ->setParameter(3, $mdp)
            ->setParameter(4, $droits);

        $queryBuilder->execute();
    }

}