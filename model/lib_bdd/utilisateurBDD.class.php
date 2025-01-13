<?php
require_once(__DIR__.'/dao.class.php');
require_once(__DIR__.'/../model_local/utilisateur.class.php');

/**
 * Requête pour interragir entre le model utilisateur local et la base de données
 */
class UtilisateurBDD{
    private static $instance = null;
    private DAO $db;
    private CONST ERROR_INSERT = 5;
    private CONST ERROR_AUTH = 7;
    private CONST ERROR_SUPPR = 10;

    private CONST ERROR_UPDATE = 5;
    private function __construct(){
        $this->db = DAO::get();
    }
    
    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new UtilisateurBDD();
        }
        return self::$instance;
    }

    /**
     * Requete pour créer un utilisateur dans la base de données une fois que toutes les informations 
     * de son compte on été validés pour insertion grace à un formulaire javascript
     * @param Utilisateur $user insère l'utilisateur passé en paramètre dans la base de données
     * @return int renvoie 0, ou un code d'erreur sinon si il y a un problème
     */
    public function createUser(string $login, string $nom,string $mdp, string $email,string $dateNaiss): int{
        // insère un utilisateur dans la base de données

        $requete = $this->db->prepare('INSERT INTO personne VALUES (:login,:nom,:mdp,:dateNaiss,:email,:dateInscr)');
        $date = new DateTime();
        $result = $requete->execute([':login' => $login,':nom' => $nom,':mdp' => password_hash($mdp,PASSWORD_BCRYPT),
        ':dateNaiss' => $dateNaiss,':email' => $email,':dateInscr' => $date->format('Y-m-d')]);

        return $result == true ?0:self::ERROR_INSERT;
    }
    /**
     * Authentifie un utilisateur et renvoie l'objet correspondant dans la base de données
     * ou alors renvoie null dans le cas ou l'authentification à echouer
     * @param string $login_user login de l'utilisateur
     * @param string $password mot de passe 
     * @param string $email mail
     * @return Utilisateur L'utilisateur associé(instance) ou null si pas fonctionné
     */
    public function authentifierUser(string $login_user, string $password, string $email = null): Utilisateur {
        $requete = $this->db->prepare('SELECT motdepasse FROM personne WHERE login = :user');
        $requete->execute([':user' => $login_user]);

        $tab = $requete->fetchAll();

        if(!count($tab)){
            throw new Exception("Le login n'est pas le bon");
        }

        if(!password_verify($password,$tab[0]['motdepasse'])){
            throw new Exception("Erreur, le mot de passe n'est pas le bon pour l'authentification !");
        }

        if(!$email){
            $requete = $this->db->prepare('SELECT login, datenaissance, email FROM personne 
            WHERE login = :logUser');
            $requete->execute([':logUser' => $login_user]);
            $result = $requete->fetchAll();
            if(count($result) == 0){
                throw new Exception("Erreur d'authentification, veuillez réassayer !");
            }else{
                return Utilisateur::getInstance($result[0]['login'],$password
                ,$result[0]['email'],new DateTime($result[0]['datenaissance']));
            }
        }else{
            $requete = $this->db->prepare('SELECT login, datenaissance, email FROM personne 
            WHERE email = :email');
            $requete->execute([':email' => $email]);
            $result = $requete->fetchAll();
            if(count($result) == 0){
                throw new Exception("Erreur d'authentification, veuillez réassayer !");
            }else{
                return Utilisateur::getInstance($result[0]['login'],$password
                ,$result[0]['email'],new DateTime($result[0]['datenaissance']));
            }
        }
        
    }
    /**
     * Met à jour les données relatives à un utilisateur, le login étant unique 
     * il suffit d'écraser dans la base de donnés les autres données relatives à un utilisateur
     * @param Utilisateur $user l'utilisateur que l'on souhaite modifier dans la base de données, toutes les données sont présentes dans l'objet
     * @return int renvoie 0, ou un code d'erreur sinon si il y a un problème
     */
    public function updateUser(Utilisateur $user): int{
        $requete = $this->db->prepare('UPDATE personne SET motdepasse = :mdp
        , datenaissance = :dateNaiss, email = :emailAdress WHERE login = :loginUser');

        $result = $requete->execute([':mdp' => password_hash($user->getMdp(),PASSWORD_BCRYPT),':dateNaiss' => $user->getDateNaiss(),
        ':emailAdress' => $user->getMail(),':loginUser' => $user->getLogin()]);
        
        return $result ? 0:self::ERROR_INSERT;
    }
    /** 
     * Supprime un utilisateur de la base de données et toutes ses données relatives 
     * @param Utilisateur $user l'utilisateur que l'on souhait supprimer 
     * @return int renvoie 0, ou un code d'erreur sinon si il y a un problème 
     */ 
    public function deleteUser(Utilisateur $user): int{
        $requete = $this->db->prepare('DELETE FROM personne WHERE login = :user');

        $result = $requete->execute([':user' => $user->getLogin()]);

        return $result ? 0:self::ERROR_SUPPR;
    }

}