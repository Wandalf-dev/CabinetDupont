<?php

namespace App\Entities;

/**
 * Classe Creneau
 * 
 * Cette classe représente un créneau horaire disponible pour un service du cabinet.
 * Elle gère les plages horaires et leur disponibilité.
 */
class Creneau {
    /** @var int|null Identifiant unique du créneau */
    private ?int $id = null;

    /** @var \DateTime Date et heure de début du créneau */
    private \DateTime $date_debut;

    /** @var \DateTime Date et heure de fin du créneau */
    private \DateTime $date_fin;

    /** @var int Identifiant du service associé */
    private int $id_service;

    /** @var bool Indique si le créneau est disponible */
    private bool $disponible;

    /**
     * Constructeur de la classe Creneau
     * Initialise un nouveau créneau comme disponible par défaut
     */
    public function __construct() {
        $this->disponible = true;
    }

    /**
     * Récupère l'identifiant du créneau
     * @return int|null L'identifiant du créneau ou null si non défini
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Définit l'identifiant du créneau
     * @param int|null $id L'identifiant à définir
     * @return self
     */
    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    /**
     * Récupère la date et l'heure de début du créneau
     * @return \DateTime
     */
    public function getDateDebut(): \DateTime {
        return $this->date_debut;
    }

    /**
     * Définit la date et l'heure de début du créneau
     * @param \DateTime $date_debut La date et l'heure de début
     * @return self
     */
    public function setDateDebut(\DateTime $date_debut): self {
        $this->date_debut = $date_debut;
        return $this;
    }

    /**
     * Récupère la date et l'heure de fin du créneau
     * @return \DateTime
     */
    public function getDateFin(): \DateTime {
        return $this->date_fin;
    }

    /**
     * Définit la date et l'heure de fin du créneau
     * @param \DateTime $date_fin La date et l'heure de fin
     * @return self
     */
    public function setDateFin(\DateTime $date_fin): self {
        $this->date_fin = $date_fin;
        return $this;
    }

    /**
     * Récupère l'identifiant du service associé
     * @return int
     */
    public function getIdService(): int {
        return $this->id_service;
    }

    /**
     * Définit l'identifiant du service associé
     * @param int $id_service L'identifiant du service
     * @return self
     */
    public function setIdService(int $id_service): self {
        $this->id_service = $id_service;
        return $this;
    }

    /**
     * Vérifie si le créneau est disponible
     * @return bool
     */
    public function isDisponible(): bool {
        return $this->disponible;
    }

    /**
     * Définit la disponibilité du créneau
     * @param bool $disponible La disponibilité à définir
     * @return self
     */
    public function setDisponible(bool $disponible): self {
        $this->disponible = $disponible;
        return $this;
    }

    /**
     * Convertit l'instance en tableau associatif
     * @return array Les données du créneau sous forme de tableau
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'date_debut' => $this->date_debut->format('Y-m-d H:i:s'),
            'date_fin' => $this->date_fin->format('Y-m-d H:i:s'),
            'id_service' => $this->id_service,
            'disponible' => $this->disponible
        ];
    }

    /**
     * Crée une instance à partir d'un tableau de données
     * @param array $data Les données à utiliser pour créer l'instance
     * @return self Une nouvelle instance de Creneau
     */
    public static function fromArray(array $data): self {
        $creneau = new self();
        if (isset($data['id'])) {
            $creneau->setId($data['id']);
        }
        if (isset($data['date_debut'])) {
            $creneau->setDateDebut(new \DateTime($data['date_debut']));
        }
        if (isset($data['date_fin'])) {
            $creneau->setDateFin(new \DateTime($data['date_fin']));
        }
        if (isset($data['id_service'])) {
            $creneau->setIdService($data['id_service']);
        }
        if (isset($data['disponible'])) {
            $creneau->setDisponible((bool) $data['disponible']);
        }
        return $creneau;
    }
}