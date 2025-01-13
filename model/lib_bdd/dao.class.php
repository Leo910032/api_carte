<?php
// Le Data Access Object 
// Il représente la base de donnée
class DAO {
  // le singleton de la classe : l'unique objet
  private static $instance = null;

  // L'objet local PDO de la base de donnée
  private PDO $db;

  // la connexion à la base de donnée
  private const database = 'pgsql:host=192.168.14.227;port=5432;dbname=archeobdd;user=admin;password=archeopass';

  // Constructeur chargé d'ouvrir la BD
  // ATTENTION: le constructeur est privé pour éviter de créer par erreur un objet DAO
  // Pour acceder à l'unique objet DAO, utiliser la méthode de classe "get".
  private function __construct() {
    try {
      $this->db = new PDO(self::database);
      //var_dump($this);
      if (!$this->db) 
        throw new Exception("Impossible d'ouvrir ".self::database);
      // Positionne PDO pour lancer les erreurs sous forme d'exeptions
      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      throw new Exception("Erreur PDO : ".$e->getMessage().' sur '.self::database);
    }
    
  }

  // Méthode statique pour acceder au singleton
  public static function get() : DAO {
    // Si l'objet n'a pas encore été crée, le crée
    if(is_null(self::$instance)) {
      self::$instance = new DAO();
    }
    return self::$instance;
  }

  // Méthode qui encapsule la préparation de la requête
  public function prepare(string $query) : PDOStatement
  {
    try {
      $request = $this->db->prepare($query);
      if ($request == FALSE) {
        die('PDO query Error on "' . $query . '"');
      }
    } catch (PDOException $e) {
      die('PDO query Error on "' . $query . '" ' . $e->getMessage());
    }
    return $request;
  }

}

