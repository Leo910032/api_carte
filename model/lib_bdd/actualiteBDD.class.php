<?php

require_once(__DIR__.'/dao.class.php');
require_once(__DIR__.'/../model_local/actualite.class.php');

/**
 * Classe permettant d'intéragir avec la base de données, avec toutes les requêtes concernant les lieux
 */
class ActualiteBDD{
    private static $instance = null;
    private DAO $db;
    private function __construct(){
        $this->db = DAO::get();
    }

    public static function getInstance(): ActualiteBDD {
        if(is_null(self::$instance)){
            self::$instance = new ActualiteBDD();
        }
        return self::$instance;
    }
    /**
     * Renvoie toutes les actualités sur un intervalle de temps donnés
     * On trie également selon le type de l'actualité par 
     * @param DateInterval $date prend un intervalle de temps dont la date doit respecter
     * @return array renvoit un vecteur d'actualités
     */
    public function getActualite(DateTime $date): array{
        $date->modify('-1 month');

        $requete = $this->db->prepare('SELECT a.*, s.dateactualite 
        FROM sederoule s JOIN actualite a ON s.idactualite = a.titre
        WHERE s.dateactualite >= :date1monthBefore');

        $requete->execute([':date1monthBefore' => $date->format('Y-m-d')]);

        $lesActualites = $requete->fetchAll();
        $lesActualitesArray = [];

        foreach($lesActualites as $actualite){
            
            array_push($lesActualitesArray,new Actualite(
            $actualite['imageactualite_link'],
            $actualite['description'],
            $actualite['titre'],
            $actualite['dateactualite'],
            $actualite['type']));
        }
        return $lesActualitesArray;
    }

    /**
     * Renvoit les informations de manière plus détaillés à propos d'une actualité
     * ici, on renvoit une actualité entière avec tous le texte
     * @param string $titre le titre de l'actualité qui est unique et qui sert pour la recherche de celle ci
     * @return array vecteur de chaine de caractère, ou une chaine de caracère est un mot
     */
    public function getActualiteInformation(string $titre): Actualite{
        $requete = $this->db->prepare('SELECT a.*, s.dateactualite 
        FROM sederoule s JOIN actualite a ON s.idactualite = a.titre
        WHERE a.titre = :title');

        $requete->execute([':title' => $titre]);

        $tab = $requete->fetchAll();
        if(count($tab) == 0){
            throw new Exception('Erreur, l\'actualité n\'a pas été trouvé');
        }

        if(count($tab) > 1){
            throw new Exception('Erreur plusieur actualités ont été renvoyés !');
        }
        return new Actualite($tab[0]['imageactualite_link'],
        $tab[0]['description'],
        $tab[0]['titre'],
        $tab[0]['dateactualite'],
        $tab[0]['type']);
    }
    /**
     * Renvois tous les types d'actualités, elles prend les types de lieux, car une actualité
     * est lié à un lieu dans notre application. 
     * @return array un tableau de tous les types d'actualités
     */
    public function getTypesActualite(): array{
        $requete = $this->db->prepare('SELECT DISTINCT type FROM actualite');
        $requete->execute();

        $tabTypes = $requete->fetchAll();
        $stringTypes = [];

        foreach($tabTypes as $types){
            array_push($stringTypes,$types[0]);
        }
        return $stringTypes;
    }

    /**
     * Renvoit toutes les lieux avec plus ou moins de précisions (Régions, ou départements, ou ville)
     * pour les filtres pour orienter les actualités
     * @return array tableau de lieu d'actualité
     */
    public function getLieuxActualite() :array{
        // SELECT nom FROM lieuvar_dump($utilisateur);
        $requete = $this->db->prepare('SELECT nom FROM lieu');
        $requete->execute();

        $tab = $requete->fetchAll();
        $stringLieu = [];

        foreach($tab as $Lieu){
            array_push($stringLieu,$Lieu[0]);
        }
        return $stringLieu;
    }

    /**
     * Toutes les actualités selon un filtre données, avec une des deux valeurs qui sont passés en filtre 
     * qui peuvent être nulle
     * @param DateInterval $date l'intervalle de temps demandés pour les actualités
     * @param string $type filtrage selon les types d'actualités (types de lieux : préhistoire, antiquités)
     * @param string $lieu les différents lieux qui sont proposés dans les filtres par le site web
     * @return array toutes les actualités correspondantes dans un tableau
     */
    public function getActualiteFiltre(DateTime $dateS, string $type = null,string $lieu = null): array{
        if(!$type && !$lieu){
            throw new Exception("Aucun élment n'ont été spécifié pour le filtre ");
        }
        $date = $dateS->modify('-1 month');
        if(!$type && $lieu){
            $requete = $this->db->prepare('SELECT a.*, s.dateactualite 
            FROM actualite a JOIN sederoule s ON a.titre = s.idactualite 
            WHERE s.idlieu = :idLieu AND s.dateactualite > :dateActu');
            $requete->execute([':idLieu'=>$lieu, ':dateActu' => $date->format('Y-m-d')]);
        }else if($type && !$lieu){
            $requete = $this->db->prepare('SELECT a.*, s.dateactualite 
            FROM actualite WHERE type = :typeActu AND s.dateactualite > :dateActu');
            $requete->execute([':typeActu'=>$type, ':dateActu' => $date->format('Y-m-d')]);
        }else{
            $requete = $this->db->prepare('SELECT a.*, s.dateactualite
            FROM actualite a JOIN sederoule s ON a.titre = s.idactualite
            WHERE s.idlieu = :idLieu AND a.type = :typeActu AND s.dateactualite > :dateActu');

            $requete->execute([':idLieu' => $lieu,':typeActu' => $type, ':dateActu' => $date->format('Y-m-d')]);
        }
        $tabActu = $requete->fetchAll();
        $lesActualite = [];
        foreach($tabActu as $actualite){
            array_push($lesActualite,new Actualite($actualite['imageactualite_link'],
            $actualite['description'],$actualite['titre'],new DateTime($actualite['dateactualite']),
            $actualite['type']));
        }
        return $lesActualite;
    }
   
}
