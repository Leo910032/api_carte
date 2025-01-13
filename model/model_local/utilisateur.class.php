<?php
require_once(__DIR__.'/GestionPack.class.php');
class Utilisateur
{
    // Attributs de l'utilisateur
    private static  $instanceUser = null; // Singleton : une seule instance
    private string $login;                            // Identifiant unique
    private string $mdp;                              // Mot de passe
    private DateTime $dateNaiss;                      // Date de naissance
    private string $mail;                             // Adresse e-mail
    private GestionPack $gestionnairePack;       // Gestionnaire de pack

    /**
     * Constructeur privé : Singleton
     * @param string $login Le login de l'utilisateur
     * @param string $mdp Le mot de passe de l'utilisateur
     * @param string $mail L'adresse e-mail de l'utilisateur
     * @param DateTime $dateNaiss La date de naissance de l'utilisateur
     */
    private function __construct(string $login, string $mdp, string $mail, DateTime $dateNaiss)
    {
        $this->login = $login;
        $this->mdp = $mdp;
        $this->mail = $mail;
        $this->dateNaiss = $dateNaiss;
        // gestionnairePack => quand on crée un utilisateur, on va créer en même temps le gestionnaire de pack
        // pour faire cela appel de la fonction dans utilisateurBDD
        // puis appel du constructeur de GestionnairePack avec dernière action et this = utilisateur
        
        //$this->gestionnairePack = new GestionPack(new DateTime(),$this);
    }

    /**
     * Setter pour le login
     * @param string $login Le login de l'utilisateur
     * @throws Exception Si le login est vide
     */
    public function setLogin(string $login): void
    {
       if (empty($login)) {
            throw new Exception("Le login ne peut pas être vide.");
        } else {
            $this->login = $login;
        }
    }

    /**
     * Setter pour le mot de passe
     * @param string $mdp Le mot de passe de l'utilisateur
     * @throws Exception Si le mot de passe est inférieur à 6 caractères
     */
    public function setMdp(string $mdp): void{
        $this->mdp = $mdp;
    }

    /**
     * Setter pour l'adresse e-mail
     * @param string $mail L'adresse e-mail de l'utilisateur
     * @throws Exception Si l'adresse e-mail est invalide
     */
    public function setMail(string $mail): void
    {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("L'adresse e-mail est invalide.");
        } else {
            $this->mail = $mail;
        }
    }

    /**
     * Setter pour la date de naissance
     * @param DateTime $dateNaiss La date de naissance de l'utilisateur
     * @throws Exception Si l'utilisateur a moins de 18 ans
     */
    public function setDateNaiss(DateTime $dateNaiss): void
    {
        if ($dateNaiss->diff(new DateTime())->y < 18) {
            throw new Exception("L'utilisateur doit avoir au moins 18 ans.");
        } else {
            $this->dateNaiss = $dateNaiss;
        }
    }

    /**
     * Getter pour le login
     * @return string Le login de l'utilisateur
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * Getter pour le mot de passe
     * @return string Le mot de passe de l'utilisateur
     */
    public function getMdp(): string
    {
        return $this->mdp;
    }

    /**
     * Getter pour l'adresse e-mail
     * @return string L'adresse e-mail de l'utilisateur
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * Getter pour la date de naissance
     * @return string La date de naissance de l'utilisateur au format 'Y-m-d'
     */
    public function getDateNaiss(): string
    {
        return $this->dateNaiss->format('Y-m-d');
    }

    /**
     * Méthode pour obtenir l'instance unique (Singleton)
     * @param string $login Le login de l'utilisateur
     * @param string $mdp Le mot de passe de l'utilisateur
     * @param string $mail L'adresse e-mail de l'utilisateur
     * @param DateTime $dateNaiss La date de naissance de l'utilisateur
     * @return Utilisateur L'instance unique de l'utilisateur
     */
    public static function getInstance(string $login = null, string $mdp = null, string $mail = null, DateTime $dateNaiss = null): Utilisateur
    {
        if (self::$instanceUser === null) {
            if($login == null || $mdp == null || $mail == null || $dateNaiss == null){
                throw new Exception("Problème, l'instance n'existe pas!, vous devez fournir des paramètres pour la créer");
            }
            self::$instanceUser = new Utilisateur($login, $mdp, $mail, $dateNaiss);
        }

        return self::$instanceUser;
    }

    /**
     * Met à jour les informations de l'utilisateur dans la base de données
     */
    public function update(): void
    {
        UtilisateurBDD::getInstance()->updateUser($this);
    }

    /**
     * Supprime l'utilisateur (réinitialise l'instance Singleton) et le supprime de la base de données
     */
    public static function delete(): void
    {
        UtilisateurBDD::getInstance()->deleteUser(self::$instanceUser);
        self::$instanceUser = null;
    }

    /**
     * Crée un utilisateur dans la base de données
     */
    public static function create() {
        UtilisateurBDD::getInstance()->createUser(self::$instanceUser);
    }

    /**Cette fonction va appeler  public function authentifierUser(string $login_user, string $password, string $email): Utilisateur {
     * 
 */
    public function authentifier(): void
    {
        $authUser = UtilisateurBDD::getInstance()->authentifierUser($this->login, $this->mdp, $this->mail); 
        if ($authUser) { 
            self::$instanceUser = $authUser; 
        } 
        throw new Exception("L'authentification a échoué."); 
    } 

    // ON VERIFIE L'AGE DANS LES VERIFICATIONS DU FORMULAIRE QUI SERA FAIT 
    // EN JAVASCRIPT
}