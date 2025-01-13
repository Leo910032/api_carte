<?php

require_once(__DIR__.'/dao.class.php');
require_once(__DIR__.'/../model_local/packrencontre.class.php');
require_once(__DIR__.'/../model_local/packvisite.class.php');
require_once(__DIR__.'/../model_local/lieu.class.php');

/**
 * Classe permettant d'intéragir avec la base de données, avec toutes les requêtes concernant le
 * profil de l'utilisateur
 */
class ProfilBDD{
    private static $instance = null;
    private DAO $db;
    private function __construct(){
        $this->db = DAO::get();
    }

    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new ProfilBDD();
        }
        return self::$instance;
    }

    /**
     * Renvoit tous les packs d'un utilisateur sous forme d'un tableau à deux dimensions
     * La premère contiens les packVisite et l'autre les packRencontre de l'utilisateur
     * @param string $login_user le login de l'utilisateur dont ont veut récuperer les pack
     * @return array tableau à deux dimensions de pack
     */
    public function readPack(string $login_user): array{
        $result = [];
        array_push($result,$this->readPackRencontre($login_user));
        array_push($result,$this->readPackVisite($login_user));
        return $result;
    }

   
    public function readPackRencontre(string $login_user): array {
        // Prépare la première requête pour récupérer les informations des rencontres sans le lieu
        $requeteRencontre = $this->db->prepare('
            SELECT r.idrencontre, r.nbpersonne, r.reduction, r.daterencontre, r.nomrencontre
            FROM possederencontre p 
            JOIN rencontre r ON p.idrencontre = r.idrencontre 
            WHERE p.idpersonne = :utilisateur
        ');
    
        // Exécute la requête avec le paramètre fourni
        $requeteRencontre->execute([':utilisateur' => $login_user]);
    
        // Récupère les résultats des rencontres
        $lesRencontres = $requeteRencontre->fetchAll();
        
        // Si aucune rencontre n'est trouvée, retourner un tableau vide
        if ($lesRencontres === false || empty($lesRencontres)) {
            return [];
        }
    
        $lesRencontresArray = [];
    
        // Parcourt les résultats pour ajouter les rencontres sans le lieu
        foreach ($lesRencontres as $rencontre) {
            // Prépare la deuxième requête pour récupérer le lieu associé à chaque rencontre
            $requeteLieu = $this->db->prepare('
                SELECT l.nom, l.prix, l.adresse, l.imageurl, l.horaire, l.type
                FROM deroulerencontre dr
                JOIN lieu l ON dr.nomlieu = l.nom
                WHERE dr.idrencontre = :idrencontre
            ');
    
            // Exécute la requête avec l'id de la rencontre
            $requeteLieu->execute([':idrencontre' => $rencontre['idrencontre']]);
    
            // Récupère les informations du lieu
            $lieu = $requeteLieu->fetch();
    
            // Si aucun lieu n'est trouvé, continuer avec le prochain
            if ($lieu === false) {
                continue;
            }
    
            // Crée l'objet Lieu
            $lieuObjet = new Lieu(
                $lieu['nom'],
                $lieu['prix'],
                $lieu['horaire'],
                $lieu['imageurl'],
                $lieu['adresse']
            );
    
            // Crée l'objet PackRencontre et l'ajoute au tableau
            $lesRencontresArray[] = new PackRencontre(
                $rencontre['nbpersonne'],
                new DateTime($rencontre['daterencontre']),
                $rencontre['nomrencontre'],
                $lieuObjet,  // Passe l'objet Lieu ici
                $rencontre['reduction'],
                $rencontre['idrencontre']
            );
        }
    
        // Retourne le tableau des rencontres avec les lieux
        var_dump($lesRencontres);  // Vérifier si des rencontres sont trouvées
        var_dump($lieu);  // Vérifier les données du lieu
    
        return $lesRencontresArray;
    }
    
    public function readPackVisite(string $login_user) : array{
        $requete = $this->db->prepare('SELECT v.* 
        FROM possedevisite p JOIN visite v ON p.idvisite = v.idvisite
        WHERE p.idpersonne = :utilisateur');

        $requete->execute([':utilisateur' => $login_user] );

        $tabVisite = $requete->fetchAll();

        if(count($tabVisite) == 0){
            return [];
        }

        $tabFinalVisite = [];

        foreach($tabVisite as $visite){
            $requete = $this->db->prepare('SELECT l.*, d.datevisite 
            FROM lieu l JOIN deroulevisite d ON l.nom = d.idlieu
            WHERE d.idvisite = :idvisit');

            $requete->execute([':idvisit' => $visite['idvisite']]);

            $tabLieuPourVisite = $requete->fetchAll();
            
            $visiteaPush = new PackVisite($visite['nomvisite']);
            $visiteaPush->setId($visite['idvisite']);

            foreach($tabLieuPourVisite as $lieu){
                $visiteaPush->ajoutLieu(new Lieu($lieu['nom'],
                $lieu['prix'],$lieu['horaire'],
            $lieu['imageurl'],$lieu['adresse']),new DateTime($lieu['datevisite']));
            }
            array_push($tabFinalVisite,$visiteaPush);
        }
        return $tabFinalVisite;
    }

    /**
     * Permet de modifier les pack existant d'un utilisateur dans la base de données, ont peut vouloir modifier seulement des packVisite
     * d'où certains paramètre sont nul, en revanche les deux ne doivent pas être null .
     * @param string $login_user le login de l'utilisateur dont on veut modifier les packs
     * @param array $packVisite les packVisite de l'utilisateur
     * @param array $packRencontre le packRencontre de l'utilisateur
     * @return int renvoie un code d'erreur et 0 sinon
     */
    public function updatePack(string $login_user,array $packVisite = null,array $packRencontre = null): int{
        error_log("Début de la méthode updatePack pour l'utilisateur : $login_user");
        if (($packVisite === null || empty($packVisite)) && ($packRencontre === null || empty($packRencontre))) {
            throw new Exception("Aucun pack n'a été pris par l'utilisateur !");
        }
        

        if($packVisite != null){
            // On supprime les packVisite de l'utilisateur
            error_log("Suppression des anciens packVisite pour l'utilisateur : $login_user");

            $requete = $this->db->prepare('SELECT * FROM possedevisite WHERE idpersonne = :idpersonne');
            $requete->execute([':idpersonne' => $login_user]);
            $packVisiteExistants = $requete->fetchAll();

            $requete = $this->db->prepare('DELETE FROM possedevisite WHERE idpersonne = :idpersonne');
            $requete->execute([':idpersonne' => $login_user]);

            $requete = $this->db->prepare('DELETE FROM deroulevisite WHERE idvisite = :idvisite');
            foreach ($packVisiteExistants as $packVisiteExistant) {
                $requete->execute([':idvisite' => $packVisiteExistant['idvisite']]);
            }

            $requete = $this->db->prepare('DELETE FROM visite WHERE idvisite = :idvisite');
            foreach ($packVisiteExistants as $packVisiteExistant) {
                $requete->execute([':idvisite' => $packVisiteExistant['idvisite']]);
            }

            // On insère les nouveaux packVisite
            foreach($packVisite as $unPackVisite) {
                $idVisite = $unPackVisite->getId();

                // Vérifier si l'idVisite existe déjà dans la table possedevisite
                $requeteCheck = $this->db->prepare('SELECT COUNT(*) FROM possedevisite WHERE idvisite = :idvisite');
                $requeteCheck->execute([':idvisite' => $idVisite]);

                // Si l'ID existe déjà, on ne fait pas l'insertion
                if ($requeteCheck->fetchColumn() > 0) {
                    error_log("Le packVisite avec ID : $idVisite existe déjà pour l'utilisateur $login_user. Insertion annulée.");
                } else {
                    // Sinon, on insère le packVisite
                    error_log("Insertion d'un packVisite avec ID : $idVisite");

                    $requeteInsert = $this->db->prepare('INSERT INTO possedevisite(idpersonne, idvisite, dateactionvisite) VALUES(:idpersonne, :idvisite, :dateactionvisite)');
                    $requeteInsert->execute([':idpersonne' => $login_user, ':idvisite' => $idVisite, ':dateactionvisite' => date('Y-m-d')]);
                    error_log("PackVisite inséré avec succès pour l'utilisateur : $login_user");
                }
            }
        }

        if($packRencontre != null){
            // Sauvegarde des informations des packRencontre existants avant de les supprimer
            $requete = $this->db->prepare('SELECT * FROM possederencontre WHERE idpersonne = :idpersonne');
            $requete->execute([':idpersonne' => $login_user]);
            $packRencontreExistants = $requete->fetchAll();

            $requete = $this->db->prepare('DELETE FROM possederencontre WHERE idpersonne = :idpersonne');
            $requete->execute([':idpersonne' => $login_user]); 

            $requete = $this->db->prepare('UPDATE rencontre SET nbpersonne = nbpersonne - 1 WHERE idrencontre = :idrencontre');
            foreach ($packRencontreExistants as $packRencontreExistant) {
                $requete->execute([':idrencontre' => $packRencontreExistant['idrencontre']]);
            }

            // On insère les nouveaux packRencontre
            foreach($packRencontre as $unPackRencontre){
                $requete = $this->db->prepare('INSERT INTO possederencontre(idpersonne,idrencontre, dateactionrencontre) VALUES(:idpersonne,:idrencontre, :dateactionrencontre)');
                $requete->execute([':idpersonne' => $login_user,':idrencontre' => $unPackRencontre->getId(), ':dateactionrencontre' => date('Y-m-d')]);

                $requete = $this->db->prepare('UPDATE rencontre SET nbpersonne = nbpersonne + 1 WHERE idrencontre = :idrencontre');
                $requete->execute([':idrencontre' => $unPackRencontre->getId()]);
            }
        }

        error_log("Fin de la méthode updatePack pour l'utilisateur : $login_user");
        return 0;

    }

    /**
     * Insère dans la base de données les pack visite et les pack rencontre de l'utilisateur
     * on incrémente de 1 le nombre de personne présent dans le groupe si cela est possible pour 1 utilisateur liés à un pack rencontre
     * @param string $login_user le login de l'utilisateur
     * @param array $Packvisite les packVisite de l'utilisateur
     * @param array $packRencontre les packRencontre de l'utilisateur
     * @return int renvoit un code d'erreur, 0 si tous c'est bien passé sinon un chiffre
     */
    public function createPack(string $login_user, array $packvisite, array $packRencontre): int{
        
        if(!$packvisite && !count($packvisite) && !$packRencontre && !count($packRencontre)){
            throw new Exception("Aucun pack n'a été pris par l'utilisateur !");
        }
        /*
            CREATION DE PACK VISITE
            les données : (idvisite | nbvisite | reduction |  nomvisite) pour table visite
            les données pour table possedevisite : (idpersonne | idvisite | dateactionvisite)
            les données pour la table deroulevisite : (idvisite | idlieu | datevisite)
        */
        $error = true;
        if($packvisite && count($packvisite)){
        foreach($packvisite as $unPackVisite){
            $requete = $this->db->prepare('INSERT INTO visite (nbvisite,reduction,nomvisite) 
            VALUES (:nbVisite,:reduction,:nomVisite) RETURNING idvisite');
            $error = $requete->execute([':nbVisite'=>$unPackVisite->getNbLieu(),':reduction'=>$unPackVisite->getReduction()
            ,':nomVisite' => $unPackVisite->getNom()]);
            $insertIdVisite = $requete->fetchColumn();
            $requete = $this->db->prepare('INSERT INTO possedevisite VALUES (:idPers,:idV,:datePackVisite)');
            $error = $requete->execute([':idPers' => $login_user,':idV'=>$insertIdVisite,':datePackVisite'=>new DateTime('now')]);

            foreach($unPackVisite->getLieux() as $lieu => $date){
                $requete = $this->db->prepare('INSERT INTO deroulevisite VALUES (:idV,:idLieu,:dateVisiteLieu)');
                $error = $requete->execute([':idV'=>$insertIdVisite,':idLieu'=>$lieu,':dateVisiteLieu'=>$date]);
            }

        }


        }

        /*
            CREATION DES PACK RENCONTRES
            Table rencontre :  (idrencontre | nbpersonne | reduction | daterencontre | nomrencontre)
            table possederencontre :  (idpersonne | idrencontre | dateactionrencontre )
            table deroulerencontre : ( idrencontre |      nomlieu     )
        */
        if($packRencontre && count($packRencontre)){
            foreach($packRencontre as $rencontre){
            $requete = $this->db->prepare('INSERT INTO rencontre (nbpersonne,reduction,daterencontre,nomrencontre)
            VALUES (:nbPers,:reduct,:dateRenc,:nomR) RETURNING idrencontre');

            $error = $requete->execute([':nbPers'=>$rencontre['nbpersonne'],':reduct'=>$rencontre['reduction'],
            [':dateRenc'=>$rencontre['daterencontre'],[':nomR'=>$rencontre['nomrencontre']]]]);

            $idRencontre = $requete->fetchColumn();
            $requete = $this->db->prepare('INSERT INTO possederencontre VALUES (:idPers,:idRencontre,:dateRenc)');
            $error = $requete->execute([':idPers'=>$login_user,':idRencontre'=>$idRencontre,':dateRenc'=>$rencontre->getDate()]);

            $requete = $this->db->prepare('INSERT INTO deroulerencontre VALUES (:idRenc,:nomLieu)');
            $error = $requete->execute([':idRenc'=>$idRencontre,':nomLieu'=>$rencontre->getLieu()->getNom()]);
            }
        }
        return $error ? 0:20;
    }

    /**
     * Supprime un packRencontre, et l'utilisateur associé on doit donc faire baisser de 1 le nombre de personne présente 
     * dans le groupe
     * @param PackRencontre $rencontre rencontre liés à un utilisateur
     * @param string $login_user le login de l'utilisateur
     * @return int renvoie 0 si tous c'est bien passé ou un code d'erreur sinon
     */
    public function deleteRencontre(PackRencontre $rencontre,string $login_user): int{
        // Vérifie si la rencontre existe pour l'utilisateur
        $requete = $this->db->prepare('SELECT COUNT(*) FROM possederencontre WHERE idpersonne = :idpersonne AND idrencontre = :idrencontre');
        $requete->execute([':idpersonne' => $login_user, ':idrencontre' => $rencontre->getId()]);
        
        if ($requete->fetchColumn() == 0) {
            throw new Exception("La rencontre n'existe pas pour cet utilisateur.");
        }

        // Supprime la rencontre de l'utilisateur dans la table possederencontre
        $requete = $this->db->prepare('DELETE FROM possederencontre WHERE idpersonne = :idpersonne AND idrencontre = :idrencontre');
        $requete->execute([':idpersonne' => $login_user, ':idrencontre' => $rencontre->getId()]);

        // Diminue de 1 le nombre de personnes dans la rencontre
        $requete = $this->db->prepare('UPDATE rencontre SET nbpersonne = nbpersonne - 1 WHERE idrencontre = :idrencontre');
        $requete->execute([':idrencontre' => $rencontre->getId()]);

        return 0;
    }

    /**
     * Suprimme un packVisite de la base de données, et tous les lieux qui lui sont associés
     * l'utilisateur est également requis pour notamment le supprimer de l'association possedeVisite
     * pour cette visite ci
     * @param PackVisite $visite la visite à supprimer de la base de données
     * @param string $login_user l'utilisateur liés à cette visite
     * @return int retourne 0 si tous c'est bien passé, un autre nombre sinon
     */
    public function deleteVisite(PackVisite $visite, string $login_user): int{

        // il faut supprimer une visite et tous ses lieux
        $result1 = $this->db->prepare('DELETE FROM deroulevisite WHERE idvisite = :idVisite')->execute([':idVisite' => $visite->getId()]);
        $result2 = $this->db->prepare('DELETE FROM possedevisite WHERE idvisite = :idVisite AND idpersonne  = :idUser')->execute([':idUser' => $login_user]);
        $result3 = $this->db->prepare('DELETE FROM visite WHERE idvisite = :idVisite')->execute([':idVisite' => $visite->getId()]);

        return $result1 && $result2 && $result3 ? 0:23;
    }

     /**
     * Permet de connaitre l'action la plus récente qu'à réaliser un utilisateur sur le site entre : 
     * packVisite,Rencontre, commentaire
     * @param string $login_user l'utilisateur dont ont veut récuperer la dernière action
     * @return DateTime la date de la dernière action
     */
    public function getDateDerniereAction(string $login_user): DateTime {
        $bd = $this->db;
        $requete = $bd->prepare('SELECT GREATEST(
            (SELECT MAX(dateactionvisite) FROM possedevisite WHERE idpersonne = :idpersonne),
            (SELECT MAX(dateactionrencontre) FROM possederencontre WHERE idpersonne = :idpersonne),
            (SELECT MAX(datecommentaire) FROM commentaire WHERE idpersonne = :idpersonne)
        )');
        $requete->execute([':idpersonne' => $login_user]);
        $result = $requete->fetch();
    
        if ($result[0] === null) {
            throw new Exception("Aucune action n'a été réalisée par l'utilisateur $login_user");
        }
    
        error_log("Date de la dernière action pour l'utilisateur $login_user : " . $result[0]);
        return new DateTime($result[0]);
    }
    
   
}