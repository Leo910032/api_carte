<?php

require_once(__DIR__.'/dao.class.php');
require_once(__DIR__.'/../model_local/commentaire.class.php');
require_once(__DIR__.'/../model_local/lieu.class.php');

/**
 * Classe permettant d'intéragir avec la base de données, avec toutes les requêtes concernant les lieux
 */
class CommentaireBDD{
    private static $instance = null;

    private DAO $db;

    private function __construct(){
        $this->db = DAO::get();
    }

    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new CommentaireBDD();
        }
        return self::$instance;
    }
    /**
     * Modifie le commentaire d'un utilisateur, un utilisateur poste des commentaires sur des 
     * lieux pour donner son avis
     * @param string $login le login de l'utilisateur dont ont veut modifier le commentaire
     * @param Commentaire $unCommentaire le commentaire que l'on souhait modifier
     * @param Lieu $lieu Le lieu depuis lequel on veut modifier
     * @return int renvoie 0 si tous c'est bien passé, ou sinon un code d'erreur
     */
    public function modifCommentaire(string $login, Commentaire $unCommentaire, Lieu $lieu): int{
        try {
            $param = [
                ':login' => $login,
                ':idCommentaire' => $unCommentaire->getIdCommentaire(),
                ':texte' => $unCommentaire->getTexte(),
                ':image_url' => $unCommentaire->getImageUrl(),
                ':date' => $unCommentaire->getDate()->format('Y-m-d H:i:s'),
                ':note' => $unCommentaire->getNote(),
                ':nomLieu' => $lieu->getNom()
            ];
            $query = 'UPDATE Commentaire c
                      SET c.texte = :texte, c.image_url = :image_url, c.date = :date, c.note = :note
                      WHERE c.idCommentaire = :idCommentaire AND EXISTS (
                          SELECT 1 FROM possedeCommentaire p
                          WHERE p.idCommentaire = c.idCommentaire AND p.login = :login AND p.nomLieu = :nomLieu
                      )';
            $stmt = $this->db->prepare($query);
            $stmt->execute($param);
            return 0;
        } catch (Exception $e) {
            return 1;
        }
    }
    /** 
     * Renvoie tous les commentaires postés depuis un lieu donné
     * @param Lieu $lieu Le lieu depuis lequel on veut récupérer 
     * @return array tous les commentaires d'un lieu
     */
    public function getCommentairesLieu(Lieu $lieu, Utilisateur $utilisateur): array {
        try {
            $param = [
                ':nomLieu' => $lieu->getNom(),
                ':login' => $utilisateur->getLogin()
            ];
            $result = $this->db->prepare('SELECT * FROM Commentaire c, possedeCommentaire p WHERE nomLieu = :nomLieu AND c.idCommentaire = p.idCommentaire AND p.login = :login');
            $result->execute($param);

            
            $commentaires = [];
            foreach ($result as $row) {
                $resultEstModif = $this->db->prepare('SELECT COUNT(*) FROM Commentaire WHERE idCommentaire = :idCommentaire AND idpersonne = :idpersonne');
                $resultEstModif->execute([':idCommentaire' => $row['idCommentaire'], ':login' => $utilisateur->getLogin()]);

                $commentaire = new Commentaire(
                    $row['texte'],
                    $resultEstModif,
                    $row['image_url'],
                    new DateTime($row['date']),
                    $row['note'],
                    idCommentaire: $row['idcommentaire']
                );
                $commentaires[] = $commentaire;
            }
            return $commentaires;
        } catch (Exception $e) {
            return [];
        }
    }
    /**
     * Créer le commentaire d'un utilisateur, un utilisateur poste des commentaires sur des 
     * lieux pour donner son avis
     * @param Commentaire $commentaire Le commentaire que l'utilisateur veut créer dans la base de données
     * @param Lieu $lieu Le lieu sur lequel on veut ajouter un commentaire
     * @param string $login_user le login de l'utilisateur qui est concerné par la création de son commentaire
     * @return int renvoie 0 si tous c'est bien passé, ou sinon un code d'erreur
     */
    public function creerCommentaire(Commentaire $commentaire,Lieu $lieu, string $login_user): int{
        
    try {
        $param = [
            ':texte' => $commentaire->getTexte(),
            ':image_url' => $commentaire->getImageUrl(),
            ':date' => $commentaire->getDate()->format('Y-m-d H:i:s'),
            ':note' => $commentaire->getNote(),
            ':nomLieu' => $lieu->getNom(),
            ':login' => $login_user
        ];
        $query = 'INSERT INTO Commentaire (texte, image_url, date, note) VALUES (:texte, :image_url, :date, :note)';
        $stmt = $this->db->prepare($query);
        $stmt->execute($param);

        $idCommentaire = $this->db->lastInsertId();
        $paramPossede = [
            ':idCommentaire' => $idCommentaire,
            ':nomLieu' => $lieu->getNom(),
            ':login' => $login_user
        ];
        $queryPossede = 'INSERT INTO possedeCommentaire (idCommentaire, nomLieu, login) VALUES (:idCommentaire, :nomLieu, :login)';
        $stmtPossede = $this->db->prepare($queryPossede);
        $stmtPossede->execute($paramPossede);

        return 0;
    } catch (Exception $e) {
        return 1;
    }
}

    /**
     * Supprime le commentaire d'un utilisateur
     * @param string $login_user 
     * @param Commentaire $leCommentaire
     * @param Lieu $lieu
     * @return int renvoie 0 si tous c'est bien passé, ou sinon un code d'erreur
     */
    public function supprimerCommentaire(string $login_user, Commentaire $leCommentaire, Lieu $lieu): int{
        try {
            $param = [
                ':login' => $login_user,
                ':idCommentaire' => $leCommentaire->getIdCommentaire(),
                ':nomLieu' => $lieu->getNom()
            ];
            $query = 'DELETE FROM Commentaire WHERE idCommentaire = :idCommentaire AND EXISTS (
                          SELECT 1 FROM possedeCommentaire p
                          WHERE p.idCommentaire = :idCommentaire AND p.login = :login AND p.nomLieu = :nomLieu
                      )';
            $stmt = $this->db->prepare($query);
            $stmt->execute($param);
            return 0;
        } catch (Exception $e) {
            return 1;
        }
    }

}