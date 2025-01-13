<?php

require_once(__DIR__.'/dao.class.php');
require_once(__DIR__.'/../model_local/lieu.class.php');
require_once(__DIR__.'/../model_local/packvisite.class.php');

/**
 * Classe permettant d'intéragir avec la base de données, avec toutes les requêtes concernant les lieux
 */
class LieuBDD{
    private static $instance = null;
    private DAO $db;
    private function __construct(){
        $this->db = DAO::get();
    }

    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new LieuBDD();
        }
        return self::$instance;
    }

    /**
     * Cette fonction renvoie tous les lieux présents dans la base de données 
     * qui appartiennent à une région précise
     * @param string $region la région, avec laquelle on veut rechercher les lieux
     * @return array renvoie tous les lieux trouvés sous formes d'un tableau
     */
    public function getLieuRegion(string $region): array{
        // renvoit tous les lieux depuis une région spécifié

        $requete = $this->db->prepare('SELECT * FROM lieu WHERE adresse LIKE ?');
        $pattern = '\'%'.$region.'%\'';
        $requete->execute([$pattern]);

        $tabLieu = $requete->fetchAll();
        $leslieux = [];
        foreach($tabLieu as $lieu){
            array_push($leslieux,new Lieu($lieu['nom'],$lieu['prix'],'',$lieu['horaire'],$lieu['imageurl'],$lieu['adresse']));
        }
        return $leslieux;
    }

    /**
     * Renvoit l'objet lieu associé à un nom de lieu de la base de données
     * @param string $nom le nom du lieu associé
     * @return Lieu renvoit un objet de lieu associé à un nom de lieu unique
     */
    public function getLieu(string $nom): Lieu{
        $requete = $this->db->prepare('SELECT * FROM lieu WHERE nom = :nomLieu');

        $requete->execute([':nomLieu' => $nom]);

        $leLieu =  $requete->fetchAll();
       
        return new Lieu($leLieu[0]['nom'],$leLieu[0]['prix'],'',$leLieu[0]['horaire'],$leLieu[0]['imageurl'],$leLieu['adresse']);
    }

    /**
     * Renvoit tous les lieux associé à une visite dans la base de données pour un utilisateur
     * @param PackVisite $visite La visite en question
     * @param string $userLogin
     * @return array renvoit tous les lieux associé à une visite
     */
    public function getLieuPourVisite(PackVisite $visite, string $userLogin): array {

        /**
         * SELECT * FROM lieu WHERE nom IN (SELECT idlieu FROM deroulevisite WHERE idvisite = 1) AND idvisite IN (SELECT ;
         */

        $requete = $this->db->prepare('SELECT * FROM lieu WHERE
         nom IN (SELECT idlieu FROM deroulevisite WHERE idvisite = :idVisiteEnCours)
         AND (SELECT count(*) FROM possedevisite WHERE idpersonne = :nomPersonne) = 1');

        $nomPersonne = '\''.$userLogin.'\'';
        $requete->execute([':idVisiteEnCours' => $visite->getId(),':nomPersonne' => $nomPersonne]);

        $tab = $requete->fetchAll();
        $lesLieux = [];

        for($i = 0;$i < count($tab);$i++){
            array_push($lesLieux,new Lieu($tab[$i]['nom'],
            $tab[$i]['prix'],'',
            $tab[$i]['horaire'],
            $tab[$i]['imageurl']
            ,$tab[$i]['adresse']));
        }
        return $lesLieux;
    }
    

}