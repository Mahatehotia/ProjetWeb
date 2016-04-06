<?php
/**
 * Created by PhpStorm.
 * User: mahatehotia
 * Date: 17/03/16
 * Time: 10:30
 */
require_once '../App/bootstrap.php';

if(strpos($_SERVER['REQUEST_URI'], 'index.php')){
    require_once '../App/bootstrap.php';
}else{
    header('Location: http://'.$_SERVER[HTTP_HOST].$_SERVER['REQUEST_URI'].'index.php');
}