<?php
/**
 * 
 */
Class Lieu{
    //Attributs d'un lieu
    private string $nom;                 // Nom du lieu
    private float $prix;                 // Prix 
    private string $lien_description;    // Lien du lieu
    private string $horaire;             // Horaire d'ouverture du lieu
    private string $lien_image;          // Lien de l'image pour le lieu
    private string $adresse;             // Adresse du lieu

     /**
     * Constructeur
     */
    public function __construct(string $nom, float $prix, string $lien_description, string $horaire, string $lien_image, string $adresse){
        $this->nom = $nom;
        $this->prix = $prix;
        $this->lien_description = $lien_description;
        $this->horaire = $horaire;
        $this->lien_image = $lien_image;
        $this->adresse = $adresse;
    }

    /**
     * Getters pour les attributs
     */
    public function getNom(): string{
        return $this->nom;
    }

    public function getImage(): string {
        return $this->lien_image;
    }

    public function getPrix(): float{
        return $this->prix;
    }

    public function getLienDescription(): string{
        return $this->lien_description;
    }

    public function getHoraire(): string{
        return $this->horaire;
    }

    public function getAdresse(): string{
        return $this->adresse;
    }    
}