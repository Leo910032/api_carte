<?php

class PackRencontre{
    //Attributs d'un pack rencontre
    private const NB_MAX_PERSONNE = 20; //Nombre maximum de personne dans le pack rencontre
    private const REDUCTION_PAR_PERSONNE = 0.01; //Reduction par personne dans le pack rencontre
    private int $nb_personne;           // Nombre de personne dans le pack rencontre
    private DateTime $date;             // Date de la rencontre
    private string $nom;                // Nom du pack rencontre
    private int $id;                 // Identifiant du pack
    private Lieu $lieu;                 // Lieu de la rencontre
    private string $idComp;

    /**
     * Constructeur
     */
    public function __construct(int $nb_personne, DateTime $date, string $nom, Lieu $lieu, int $id = -1)
    {
        $this->nb_personne = $nb_personne;
        $this->date = $date;
        $this->nom = $nom;
        $this->lieu = $lieu;
        $this->idComp = uniqid();
        /**
         * L'identifiant vaut -1 si jamais on créé le pack 
         * sinon il vaut l'identifiant présent dans la base de données
         */
        $this->id = $id;
    }

    /**
     * Getters pour les attributs
     */
    public function getNbPersonne(): int
    {
        return $this->nb_personne;
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrix(): float{
        return $this->lieu->getPrix();
    }

    public function getPrixReduit(): float{
        return $this->getPrix() * (self::REDUCTION_PAR_PERSONNE * $this->nb_personne);
    }

    public function getLieux(): Lieu{
        return $this->lieu;
    }

    public function getId(): string{
        return $this->id;
    }

    public function getReduction(): float{
        return self::REDUCTION_PAR_PERSONNE * $this->nb_personne;
    }

    /**
     * Setters pour les attributs
     */
    public function setDate(DateTime $date){
        $this->date = $date;
    }

    public function addPersonne(){
        if ($this->nb_personne + 1 > self::NB_MAX_PERSONNE) {
            throw new Exception("Nombre de personne maximum atteint");
        } else {
            $this->nb_personne++;
        }
    }

    public function getIdComp(){
        return $this->idComp;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

}