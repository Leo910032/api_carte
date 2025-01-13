<?php

require_once(__DIR__.'/dao.class.php');
require_once(__DIR__.'/../model_local/packvisite.class.php');

/**
 * Classe permettant d'intéragir avec la base de données, avec toutes les requêtes concernant les lieux
 */
class PackVisiteBDD{
    private static $instance = null;
    private DAO $db;
    private function __construct(){
        $this->db = DAO::get();
    }
    
    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new PackVisiteBDD();
        }
        return self::$instance;
    }
    /**
     * Les regarde si les dates associé au lieux d'une visite ne sont pas déjà présentes dans la base 
     * @param PackVisite $visite
     * @param string $login_user
     * @return bool renvoit false si ce n'est pas cohérent en terme de date
     */
    public function checkCoherencePackVisite(PackVisite $visite,string $login_user) : bool{
        $dateVisiteUser = $visite->getDateVisite();

        $requete = $this->db->prepare('SELECT d.datevisite
        FROM deroulevisite d JOIN possedevisite p ON d.idvisite = p.idvisite 
        WHERE p.idpersonne = :loginUser');

        $requete->execute([':loginUser' => $login_user]);

        $tabDate = $requete->fetchAll();

        foreach($tabDate as $date){
            foreach($dateVisiteUser as $dateVisite){
                if($dateVisite->format('Y-m-d') == $date['datevisite']){
                    return false;
                }
            }
        }
        return true;
    }
    
}