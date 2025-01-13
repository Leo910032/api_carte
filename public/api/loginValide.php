<?php

require_once(__DIR__.'/../../model/lib_bdd/dao.class.php');

function getName(string $login): string{

    $dao = DAO::get();
    $requete = $dao->prepare('SELECT login FROM personne WHERE login = :user');
    
    $requete->execute([':user'=> $login]);

    $tab = $requete->fetchAll();

    if(count($tab) == 0){
        return "";
    }else{
        return $tab[0]['login'];
    }
}

// Indique dans le header que l'on sort du JSON
//header('Content-Type: application/json; charset=utf-8');
//print(//json_encode(['login'=>getName($_GET['login'])]));
