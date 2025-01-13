<?php

/**
 * Classe représentant un commentaire.
 */
class Commentaire {
    // Propriétés privées de la classe
    private string $texte;
    private bool $estModifUtilisateur;
    private string $image_url;
    private DateTime $date;
    private int $note;
    private int $idCommentaire;
    private string $login_user;
    private CONST noteMAX = 5;
    private CONST noteMIN = 1;

    /**
     * Constructeur de la classe Commentaire.
     *
     * @param string $texte Texte du commentaire.
     * @param bool $estModifUtilisateur Indique si l'utilisateur a modifié le commentaire.
     * @param string $image_url URL de l'image associée au commentaire.
     * @param DateTime $date Date du commentaire.
     * @param int $note Note attribuée au commentaire.
     * @param int $idCommentaire Identifiant unique du commentaire.
     */
    public function __construct(string $texte, bool $estModifUtilisateur, string $image_url, DateTime $date, int $note, int $idCommentaire = -1) {
        $this->texte = $texte;
        $this->estModifUtilisateur = $estModifUtilisateur;
        $this->image_url = $image_url;
        $this->date = $date;
        $this->note = $note;
        // un id de -1 indique que le commentaire n'est pas encore dans la base de données
        $this->idCommentaire = $idCommentaire;
    }

    // Méthodes Getters

    /**
     * Récupère le texte du commentaire.
     *
     * @return string
     */
    public function getTexte(): string {
        return $this->texte;
    }

    /**
     * Vérifie si le commentaire peut etre modifier par le user
     *
     * @return bool
     */
    public function getEstModifiableUser(): bool {
       return $this->estModifUtilisateur;
    }

    /**
     * Récupère la date du commentaire.
     *
     * @return DateTime
     */
    public function getDate(): string {
        return $this->date->format('Y-m-d');
    }

    /**
     * Récupère la note du commentaire.
     *
     * @return int
     */
    public function getNote(): int {
        return $this->note;
    }

    public function getId()  {
        return $this->idCommentaire;
    }

    /**
     * Récupère l'URL complète de l'image associée au commentaire.
     *
     * @return string
     */
    public function getImgComplet(): string {
        return $this->image_url;
    }

    // Méthodes Setters

    /**
     * Définit une nouvelle note pour le commentaire.
     *
     * @param int $note Nouvelle note.
     */
    public function setNote(int $note): void {
        if($note > self::noteMAX || $note < self::noteMIN){
            throw new Exception("Erreur, la note $note ne respecte pas l'intervalle: (".self::noteMIN.','.self::noteMAX.')');
        }
        $this->note = $note;
    }
    /**
     * Appelle la méthode modifCommentaire de la base de données afin de modifier le commentaire de l'utilisateur
     * @param string $text
     * @return void
     */
    public function setText(string $text): void {
        $this->texte = $text;
    }
    /**
     * Supprime le commentaire (action logique).
     */
    public function delete(): void {
        //appel la methode du frame work lib_bdd
        // on utilise unset($this) pour supprimer l'objet en lui même
    }
}

?>
