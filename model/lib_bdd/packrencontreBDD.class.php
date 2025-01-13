<?php

require_once(__DIR__.'/dao.class.php');
require_once(__DIR__.'/../model_local/packrencontre.class.php');
require_once(__DIR__.'/../model_local/lieu.class.php');

/**
 * Classe permettant d'intéragir avec la base de données, avec toutes les requêtes concernant les 
 * rencontres qu'un utilisateur va prendre
 */
class PackRencontreBDD{
    private static $instance = null;
    private DAO $db;
    private function __construct(){
        $this->db = DAO::get();
    }

    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new PackRencontreBDD();
        }
        return self::$instance;
    }

    /**
     * On vérifie qu'un pack rencontre que l'utilisateur a pris, n'a pas de problème de date
     * si deux pack rencontre sont le même jour. On vérifie donc que le packRencontre passé en paramètre
     * ne possède pas une date identique à une autre date présente pour un pack rencontre de l'utilisateur dans
     * la base de données
     * @param string $login_user le login de l'utilisateur pour lequel on veut vérifier
     * @param PackRencontre $packRencontre le packRencontre
     * @return bool un message approprié si il y a une incohérence, sinon une chaine vide
     */
    public function checkPackRencontreCoherence(string $login_user, PackRencontre $packRencontre): bool{
        $requete = $this->db->prepare('SELECT r.daterencontre 
        FROM rencontre r JOIN possederencontre p ON r.idrencontre = p.idrencontre WHERE p.idpersonne = :loginUser');

        $requete->execute([':loginUser' => $login_user]);

        $lesDatesRencontres = $requete->fetchAll();
        
        foreach($lesDatesRencontres as $rencontreDate){
            if($rencontreDate['daterencontre'] == $packRencontre->getDate()){
                return false;
            }
        }
        return true;
    }   
    /**
     * Renvoit toutes les packRencontres disponibles pour un lieu, lors de la recherche d'un utilisateur
     * @param Lieu $lieu le lieu depuis lequel on veut récupérer les pack rencontres
     * @return array renvoit tous les packRencontres, ou un vecteur vide si il n'y en a pas
     */
    public function getRencontreFromLieu(Lieu $lieu): array{
        $requete = $this->db->prepare('SELECT r.idrencontre,r.nbPersonne,r.daterencontre,r.nomrencontre,d.nomlieu 
        FROM rencontre r JOIN deroulerencontre d ON r.idrencontre = d.idrencontre WHERE d.nomlieu = :namePlace AND r.nbpersonne < 30');

        $requete->execute([':namePlace' => $lieu->getNom()]);
        
        $tabRencontre = $requete->fetchAll();
        $lesRencontres = [];

        if(!count($tabRencontre)){
            return [];
        }

        foreach($tabRencontre as $rencontre){
            array_push($lesRencontres,new PackRencontre($rencontre['nbpersonne'],
            new DateTime($rencontre['daterencontre']),
            $rencontre['nomrencontre'],new Lieu($lieu->getNom(),
            $lieu->getPrix(),$lieu->getLienDescription(),$lieu->getHoraire(),
            $lieu->getImage(),$lieu->getAdresse()),$rencontre['idrencontre']));
        }
        return $lesRencontres;
    }
   
}