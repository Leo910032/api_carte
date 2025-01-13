<?php

/**
 * Classe représentant une actualité.
 */
class Actualite {
    // URL de l'image associée à l'actualité
    private string $img_url;

    // Texte de l'actualité
    private string $texte;

    // Nom ou titre de l'actualité
    private string $nom;

    private string $type;

    // Date de l'actualité (au format YYYY-MM-DD ou similaire)
    private DateTime $date;

    private const CHEMIN_URL = 'ARCHEOPASS/public/img_actualite'; 
    /**
     * Constructeur de la classe Actualite.
     * 
     * @param string $img_url URL de l'image associée à l'actualité.
     * @param string $texte Texte décrivant l'actualité.
     * @param string $nom Nom ou titre de l'actualité.
     * @param dateTime $date Date de l'actualité.
     */
    public function __construct(string $img_url, string $texte, string $nom, DateTime $date, string $type) {
        $this->img_url = $img_url;
        $this->texte = $texte;
        $this->nom = $nom;
        $this->date = $date;
        $this->type = $type;
    }

    /**
     * Récupère le texte de l'actualité.
     * 
     * @return string Le texte de l'actualité.
     */
    public function getTexte(): string {
        return $this->texte;
    }

    /**
     * Récupère le nom ou titre de l'actualité.
     * 
     * @return string Le nom ou titre de l'actualité.
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * Récupère la date de l'actualité.
     * 
     * @return string La date de l'actualité.
     */
    public function getDate(): string {
        return $this->date->format('Y-m-d');
    }

    /**
     * Récupère le type de l'actualité (non défini dans les propriétés actuelles).
     * 
     * @return string|null Le type de l'actualité ou null si non défini.
     */
    public function getType(): ?string {
        return $this->type; 
    }

    /**
     * Récupère le lieu de l'actualité (non défini dans les propriétés actuelles).
     * 
     * @return string|null Le lieu de l'actualité ou null si non défini.
     */
    public function getLieu(): string {
        return $this->lieu ?? null; 
    }

    /**
     * Récupère l'URL complète de l'image associée à l'actualité.
     * 
     * @return string L'URL complète de l'image.
     */
    public function getImgComplet(): string {
        return self::CHEMIN_URL.$this->img_url;
    }
}

?>
