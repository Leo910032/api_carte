<?php
require_once(__DIR__.'/actualite.class.php');

class GestActualite {
    // Singleton instance pour garantir une seule instance de cette classe
    private static  $instance = null;

    // Liste des types d'actualités
    private array $lesTypes;

    // Liste des lieux d'actualités
    private array $lesLieux;

    // Tableau pour stocker toutes les actualités
    private array $lesActualites;

    /**
     * Constructeur privé pour empêcher l'instanciation directe.
     * Il initialise les types et lieux en appelant les méthodes de la classe ActualiteBDD.
     */
    private function __construct() {
        // Initialisation des types d'actualités
        $this->lesTypes = ActualiteBDD::getInstance()->getTypesActualite();

        // Si `getActualiteFiltre()` est appelée sans paramètre, elle retourne toutes les actualités
        $this->lesLieux = ActualiteBDD::getInstance()->getActualiteFiltre();
    }

    /**
     * Méthode statique pour obtenir l'instance unique de la classe (Singleton).
     *
     * @return GestActualite L'instance unique de la classe.
     */
    public static function getInstance(): GestActualite {
        if (self::$instance === null) {
            self::$instance = new GestActualite();
        }
        return self::$instance;
    }

    /**
     * Filtrer les actualités selon le type et/ou le lieu.
     *
     * @param string|null $leType Le type d'actualité (exemple: "Sport", "Politique").
     * @param string|null $lieu   Le lieu des actualités (exemple: "Paris", "Grenoble").
     * @return array              Un tableau d'objets Actualite correspondant aux critères de filtrage.
     */
    public function filtrer(string $leType, string $lieu): array {
        // Filtrage des actualités selon le type et le lieu
        return actualiteBDD::getInstance()->getActualiteFiltre($leType, $lieu);
    }

    /**
     * Obtenir les types d'actualités disponibles.
     *
     * @return array Un tableau contenant tous les types d'actualités.
     */
    public function getLesTypes(): array {
        return $this->lesTypes;
    }

    /**
     * Obtenir les lieux des actualités disponibles.
     *
     * @return array Un tableau contenant tous les lieux disponibles.
     */
    public function getLesLieux(): array {
        return $this->lesLieux;
    }
}
