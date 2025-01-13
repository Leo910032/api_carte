<?php
require_once(__DIR__.'/../model_local/lieu.class.php');
//require_once(__DIR__.'');
/**
 * Classe PackVisite - Gère un pack de visites de lieux avec des dates spécifiques
 */
class PackVisite
{
    /** @var int Nombre maximum de lieux autorisés */
    private const NB_LIEU_MAX = 5; // Valeur exemple, à ajuster selon les besoins

    /** @var string Nom unique du pack de visite */
    private string $nom;

    /** @var int Nombre de lieux dans le pack */
    private int $nbLieu;

    /** @var float Réduction appliquée par lieu */
    private const REDUCTION_PAR_LIEU = 0.1; // 10% de réduction par lieu, à ajuster

    /** @var array<Lieu> Tableau des lieux à visiter */
    private array $lieux;
    private int $id;
    private string $idComp;

    /**
     * Constructeur de PackVisite
     * 
     * @param Lieu $lieu Premier lieu à ajouter au pack
     * @param DateTime $date Date de la visite
     * @param int $nbLieu Nombre initial de lieux (par défaut 1)
     * @throws Exception Si le nombre de lieux dépasse NB_LIEU_MAX
     */
    public function __construct(string $nom,DateTime $date = null,Lieu $lieu = null,int $nbLieu = 1, int $id = -1){
        $this->nom = $nom;  // À initialiser via setNom()
        $this->nbLieu = $nbLieu;
        $this->id = $id;
        $this->idComp = uniqid();
        if($lieu == null || $date == null){
            $this->lieux = [];
            $this->nbLieu = 0;
        }else{
            $this->lieux = [];
            $this->lieux = [$lieu->getNom() => $date->format('Y-m-d')];
            $this->nbLieu = 1;
        }
        
    }

    /**
     * Ajoute un nouveau lieu au pack avec sa date de visite si inf a NB_LIEU_MAX
     * 
     * @param Lieu $lieu Lieu à ajouter
     * @param DateTime $date Date de la visite
     * @throws Exception Si le nombre maximum de lieux est atteint
     */
    
    public function ajoutLieu(Lieu $lieu, DateTime $date): void {
        if ($this->nbLieu >= self::NB_LIEU_MAX) {
            throw new Exception("Nombre maximum de lieux atteint");
        }
        if (array_key_exists($lieu->getNom(), $this->lieux)) {
            throw new Exception("Le lieu existe déjà dans le pack");
        }
        $this->lieux[$lieu->getNom()] = $date->format('Y-m-d');
        $this->nbLieu++;
    }
      

    /**
     * Supprime un lieu du pack
     * 
     * @param Lieu $lieu Lieu à supprimer
     * @throws Exception Si le lieu n'existe pas dans le pack
     */

    public function supprLieu(Lieu $lieu): void {
        $lieuNom = $lieu->getNom();
        if (!array_key_exists($lieuNom, $this->lieux)) {
            throw new Exception("Le lieu n'existe pas dans le pack");
        }
        unset($this->lieux[$lieuNom]);
        $this->nbLieu--;
    }

    public function getIdComp(){
        return $this->idComp;
    }
    
    /**
     * @return string Nom du pack
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom Nouveau nom du pack
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * Modifie la date
     * 
     * @param DateTime $date Nouvelle date
     * @param Lieu $lieu Lieu concerné
     * @throws Exception Si le lieu n'existe pas dans le pack
     */

    public function setDate(DateTime $date, Lieu $lieu): void {
        $lieuNom = $lieu->getNom();
        if (!array_key_exists($lieuNom, $this->lieux)) {
            throw new Exception("Le lieu n'existe pas dans le pack");
        }
        $this->lieux[$lieuNom] = $date->format('Y-m-d');
    }

    public function setId(int $id){
        $this->id = $id;
    }

    /**
     * @return int Nombre de lieux dans le pack
     */
    public function getNbLieu(): int
    {
        return $this->nbLieu;
    }

    /**
     * @return array<DateTime> Tableau des dates de visite
     */
    public function getDateVisite(): array {
        return array_values($this->lieux);
    }

    public function getReduction(): float
    {
        return self::REDUCTION_PAR_LIEU * $this->nbLieu;
    }

    /**
     * Modifie un lieu du pack
     * 
     * @param Lieu $ancienLieu Lieu à modifier
     * @param Lieu $nouveauLieu Nouveau lieu
     * @throws Exception Si l'ancien lieu n'existe pas dans le pack
     */

    public function modifLieu(Lieu $ancienLieu, Lieu $nouveauLieu): void {
        $ancienLieuNom = $ancienLieu->getNom();
        if (!array_key_exists($ancienLieuNom, $this->lieux)) {
            throw new Exception("Le lieu n'existe pas dans le pack");
        }
        $date = $this->lieux[$ancienLieuNom];
        unset($this->lieux[$ancienLieuNom]);
        $this->lieux[$nouveauLieu->getNom()] = $date;
    }

    /**
     * Calcule le prix réduit du pack
     * 
     * @return float Prix après réduction
     */
    public function getPrixReduit(): float
    {
        return $this->getPrix()*$this->getReduction();
    }

    public function getId() : int{
        return $this->id;
    }

    /**
     * Calcule le prix total du pack sans réduction
     * 
     * @return float Prix total
     */
    public function getPrix(): float
    {
        $prix = 0;
        foreach($this->lieux as $unLieu){
            $prix += $unLieu->getPrix();
        }
        return $prix;
    }

    public function getLieux(): array {
        return $this->lieux;
    }
}