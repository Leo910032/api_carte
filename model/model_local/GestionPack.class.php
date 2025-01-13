<?php
require_once(__DIR__ . '/utilisateur.class.php');
require_once(__DIR__.'/../lib_bdd/profilBDD.class.php');
class GestionPack{
    private DateTime $dateDernierAction;
    private Utilisateur $unUtilisateur;
    private array $rencontres;
    private array $visites;
    /**
     * Constructor
     * @param DateTime $dateLastAction
     * @param Utilisateur $user
     */
    public function __construct(DateTime $dateLastAction, Utilisateur $user){
        $this->rencontres = [];
        $this->visites = [];
        $this->unUtilisateur = $user;
        $this->dateDernierAction = $dateLastAction;
    }

    /**
     * Create operation
     * @return bool
     */
    public function create(): bool{
        //appel la fonction dans le framework lib_bdd
        ProfilBDD::getInstance()->createPack($this->unUtilisateur->getLogin(), $this->visites, $this->rencontres);
    }

    /**
     * Update operation
     * @return bool
     */
    public function update(): bool
    {
        //appel la fonction dans le framework lib_bdd
        ProfilBDD::getInstance()->updatePack($this->unUtilisateur->getLogin(), $this->visites, $this->rencontres);
    }
    /**
     * Read operation
     * @return bool
     */
    public function read(): bool{
        $this->rencontres = ProfilBDD::getInstance()->readPackRencontre($this->unUtilisateur->getLogin());
        $this->visites = ProfilBDD::getInstance()->readPackVisite($this->unUtilisateur->getLogin());
    }

    /**
     * Clear the pack
     * @return void
     */
    //Cette fonction va supprimer tout les packVisites et et packRencotre dans 
    // les tableau correspond. 
    public function packClear(): void{
        // on vide les pack de la base de données pour un utilisateur
        foreach($this->rencontres as $rencontre){
            ProfilBDD::getInstance()->deleteRencontre($rencontre, $this->unUtilisateur->getLogin());
        }

        foreach($this->visites as $visite){
            ProfilBDD::getInstance()->deleteVisite($visite, $this->unUtilisateur->getLogin());
        }
        unset($this->rencontres);
        unset($this->visites);
    }

    /**
     * Check visit date coherence
     * @return bool
     */
    //Cette fonction va prendre les deux vecteur rencontre et vistes et va verifier si dans les deux vecteur 
    //il une viste ou une rencontre qui ont la meme date si oui return false(pas coherent) true (coherent pas de doublons)
    //On ne peut avoit qu'une seule viste/rencotre par jours
    public function checkCoherenceDateRenconte(): bool{
        // on vérifie que dans le packvisite, aucune date n'est dupliquée
        for($i = 0; $i < count($this->rencontres);$i++){
            for($j = 1; $j < count($this->rencontres);$j++){
                if($this->rencontres[$i]->getDate() == $this->rencontres[$j]->getDate()){
                    return false;
                }
            }
        }
        return true;
    }

    public function checkCoherenceDateVisite(): bool{
        // on vérifie que dans le packvisite, aucune date n'est dupliquée
        for($i = 0;$i < count($this->visites);$i++){
            for($j = 0;$j < count($this->visites);$j++){
                foreach($this->visites[$i]->getDateVisite() as $dateVisiteI){
                    foreach($this->visites[$j]->getDateVisite() as $dateVisiteJ){
                        if($dateVisiteI == $dateVisiteJ){
                            return false;
                        }
                    }
                }
            }
        }
        // on vérifie qu'une meme date sois présente dans le pack visite et rencontre en meme temps
        return true;
    }

    public function ajoutRencontre(PackRencontre $rencontre){
        if($this->nomRencontrePresent($rencontre->getNom())){
            throw new Exception('l\'utilisateur possède déjà cette rencontre : '.$rencontre->getNom());
        }
        array_push($this->rencontres,$rencontre);
    }

    public function ajoutVisite(PackVisite $visite){
        if($this->nomVisitePresent($visite->getNom())){
            throw new Exception('L\'utilisteur possède déjà la visite : '.$visite->getNom().'Comme nom de visite');
        }
        array_push($this->visites,$visite);
    }

    public function supprRencontre(PackRencontre $rencontre){
        foreach($this->rencontres as $rencontreCours){
            if($rencontreCours->getIdComp() == $rencontre->getIdComp()){
                unset($rencontreCours);
                break;
            }
        }
        ProfilBDD::getInstance()->deleteRencontre($rencontre,$this->unUtilisateur->getLogin());
    }

    public function supprVisite(PackVisite $visite){
        foreach($this->visites as $visiteCours){
            if($visiteCours->getIdComp() == $visite->getIdComp()){
                unset($visiteCours);
                break;
            }
        }
        ProfilBDD::getInstance()->deleteVisite($visite,$this->unUtilisateur->getLogin());
    }

    public function modifRencontre(PackRencontre $Rencontrelast, PackRencontre $newRencontre){
        $db = DAO::get();
        $requete = $db->prepare('UPDATE rencontre SET nbpersonne = :nbpersonne, daterencontre = :daterencontre, nomrencontre = :nomrencontre, reduction = :reduction WHERE idrencontre = :idrencontre');
        $requete->bindValue(':nbpersonne', $newRencontre->getNbPersonne());
        $requete->bindValue(':daterencontre', $newRencontre->getDate());
        $requete->bindValue(':nomrencontre', $newRencontre->getNom());
        $requete->bindValue(':reduction', $newRencontre->getReduction());
        $requete->bindValue(':idrencontre', $Rencontrelast->getId());
        $requete->execute();
    }

    public function modifVisite(PackVisite $visiteLast, PackVisite $visiteNew){
        $db = DAO::get();
        $requete = $db->prepare('UPDATE visite SET nomvisite = :nomvisite, reduction = :reduction WHERE idvisite = :idvisite');
        $requete->bindValue(':nomvisite', $visiteNew->getNom());
        $requete->bindValue(':reduction', $visiteNew->getReduction());
        $requete->bindValue(':idvisite', $visiteLast->getId());
        $requete->execute();
    }

    /**
     * Check general date coherence
     * @return bool
     */

    // Cette fonction prend le tableau de rencontre (rencontres) et verifie qu'aucune renncotre ai le meme nom
    //L'utilisateur ne va pas voir l'id mais il va voir le nom et donc doit etre unique
    //Aussi qu'on on cree un nouveau objet PackRencotre le nom doit pas etre le meme
    public function nomRencontrePresent(string $nomRencontre): bool{
        foreach($this->rencontres as $rencontre){
            if($rencontre->getNom() == $nomRencontre){
                return true;
            }
        }
        return false;
    }

    // Cette fonction prend le tableau de visite (visites) et verifie qu'aucune visite ai le meme nom
    //L'utilisateur ne va pas voir l'id mais il va voir le nom et donc doit etre unique
    //Aussi qu'on on cree un nouveau objet PackVisite le nom doit pas etre le meme
    public function nomVisitePresent(string $nomVisite): bool{
        foreach($this->visites as $visite){
            if($visite->getNom() == $nomVisite){
                return true;
            }
        }
        return false;
    }

    public function getDateLastAction(): string{
        return $this->dateDernierAction->format('Y-m-d');
    }

    /**
     * Retourne un tableau de vistes
     */
    public function getPackVisite(): array{
        return $this->visites;
    }

    /**
     * retourne un tablea de rencontre
     */
    public function getPackRencontre(): array{
        return $this->rencontres;
    }
}