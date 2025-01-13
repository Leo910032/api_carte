<?php
require_once(__DIR__.'/commentaire.class.php');
require_once(__DIR__.'/lieu.class.php');
require_once(__DIR__.'/../lib_bdd/lieuBDD.class.php');
require_once(__DIR__.'/utilisateur.class.php');
require_once(__DIR__.'/../model_local/lieu.class.php');
require_once(__DIR__.'/../lib_bdd/commentaireBDD.class.php');

/**
 * Classe permettant de gérer les commentaires.
 */
class GestionnaireCommentaire {
    // Instance unique de la classe (Singleton)
    private static $instanceComm = null;
    private array $commentaires; 
    private Lieu $lieu;
    private Utilisateur $user;

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct(Lieu $unlieu, Utilisateur $user) {
        // on récupère tous les commentaires postés sur un lieu
        // qui plus est le commentaire passé en paramètre
        $this->commentaires = [];
        $this->user = $user;
        $this->lieu = $unlieu;
        $dao = DAO::get();
        $requete = $dao->prepare('SELECT * FROM commentaire NATURAL JOIN possedecommentaire 
        WHERE nomlieu = :nameLieu ');
        $requete->execute([':nameLieu' => $unlieu->getNom()]);

        $tabCommentaire = $requete->fetchAll();

        foreach($tabCommentaire as $commentaire){

            $requete = $dao->prepare('SELECT idpersonne FROM commentaire WHERE idcommentaire = :idCom
            AND idpersonne = :idpers');

            $requete->execute([':idCom' => $commentaire['idcommentaire'],':idpers' => $user->getLogin()]);

            $estModif = count($requete->fetchAll()) == 0 ? false:true;

            array_push($this->commentaires,
            new Commentaire(
            $commentaire['texte'],
            $estModif,
            $commentaire['image_url'],
            $commentaire['datecommentaire'],
        $commentaire['note'],
    $commentaire['idcommentaire']));
        }
    }

    /**
     * Méthode pour obtenir l'instance unique de GestionnaireCommentaire (Singleton).
     *
     * @param Lieu|null $le_lieu Optionnel : Lieu pour lequel gérer les commentaires.
     * @return GestionnaireCommentaire
     */
    public static function getInstance(Lieu $le_lieu,Utilisateur $user): GestionnaireCommentaire {
        if (!self::$instanceComm) {
            self::$instanceComm = new GestionnaireCommentaire($le_lieu,$user);
        }
        return self::$instanceComm;
    }

    /**
     * Crée un nouveau commentaire.
     *
     * @param Commentaire $commentaire Le commentaire à ajouter.
     * @param Utilisateur $user L'utilisateur qui crée le commentaire.
     */
    public function createMessage(Commentaire $commentaire): void {
        CommentaireBDD::getInstance()->creerCommentaire($commentaire,$this->lieu,$this->user->getLogin());
    }

    /**
     * Modifie un commentaire existant.
     *
     * @param Commentaire $commentaire Le commentaire à modifier.
     * @param Utilisateur $user L'utilisateur qui modifie le commentaire.
     */
    public function modifMessage(Commentaire $commentaire): void {
        CommentaireBDD::getInstance()->modifCommentaire($this->user->getLogin(),$commentaire,$this->lieu);
    }
}


