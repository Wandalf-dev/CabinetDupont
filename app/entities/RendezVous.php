<?php

namespace App\Entities;

/**
 * Classe RendezVous
 * 
 * Cette classe représente un rendez-vous pris par un utilisateur pour un créneau spécifique.
 * Elle gère les informations relatives au rendez-vous ainsi que son statut.
 */
class RendezVous {
    /** @var int|null Identifiant unique du rendez-vous */
    private ?int $id = null;

    /** @var int Identifiant de l'utilisateur qui a pris le rendez-vous */
    private int $id_user;

    /** @var int Identifiant du créneau réservé */
    private int $id_creneau;

    /** @var string Motif ou raison du rendez-vous */
    private string $motif;

    /** @var string Statut actuel du rendez-vous (en_attente, confirme, annule) */
    private string $statut;

    /** @var \DateTime|null Date de création du rendez-vous */
    private ?\DateTime $date_creation;

    /**
     * Constructeur de la classe RendezVous
     * Initialise un nouveau rendez-vous avec un statut 'en_attente' et la date actuelle
     */
    public function __construct() {
        $this->date_creation = new \DateTime();
        $this->statut = 'en_attente';
    }

    /**
     * Récupère l'identifiant du rendez-vous
     * @return int|null L'identifiant du rendez-vous ou null si non défini
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Définit l'identifiant du rendez-vous
     * @param int|null $id L'identifiant à définir
     * @return self
     */
    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    /**
     * Récupère l'identifiant de l'utilisateur
     * @return int L'identifiant de l'utilisateur
     */
    public function getIdUser(): int {
        return $this->id_user;
    }

    /**
     * Définit l'identifiant de l'utilisateur
     * @param int $id_user L'identifiant de l'utilisateur
     * @return self
     */
    public function setIdUser(int $id_user): self {
        $this->id_user = $id_user;
        return $this;
    }

    /**
     * Récupère l'identifiant du créneau
     * @return int L'identifiant du créneau
     */
    public function getIdCreneau(): int {
        return $this->id_creneau;
    }

    /**
     * Définit l'identifiant du créneau
     * @param int $id_creneau L'identifiant du créneau
     * @return self
     */
    public function setIdCreneau(int $id_creneau): self {
        $this->id_creneau = $id_creneau;
        return $this;
    }

    /**
     * Récupère le motif du rendez-vous
     * @return string Le motif du rendez-vous
     */
    public function getMotif(): string {
        return $this->motif;
    }

    /**
     * Définit le motif du rendez-vous
     * @param string $motif Le motif du rendez-vous
     * @return self
     */
    public function setMotif(string $motif): self {
        $this->motif = $motif;
        return $this;
    }

    /**
     * Récupère le statut du rendez-vous
     * @return string Le statut actuel du rendez-vous
     */
    public function getStatut(): string {
        return $this->statut;
    }

    /**
     * Définit le statut du rendez-vous
     * @param string $statut Le nouveau statut ('en_attente', 'confirme', 'annule')
     * @return self
     * @throws \InvalidArgumentException Si le statut n'est pas valide
     */
    public function setStatut(string $statut): self {
        $statuts_valides = ['en_attente', 'confirme', 'annule'];
        if (!in_array($statut, $statuts_valides)) {
            throw new \InvalidArgumentException("Statut non valide");
        }
        $this->statut = $statut;
        return $this;
    }

    /**
     * Récupère la date de création du rendez-vous
     * @return \DateTime|null La date de création ou null si non définie
     */
    public function getDateCreation(): ?\DateTime {
        return $this->date_creation;
    }

    /**
     * Définit la date de création du rendez-vous
     * @param \DateTime|null $date_creation La date de création à définir
     * @return self
     */
    public function setDateCreation(?\DateTime $date_creation): self {
        $this->date_creation = $date_creation;
        return $this;
    }

    /**
     * Convertit l'instance en tableau associatif
     * @return array Les données du rendez-vous sous forme de tableau
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'id_user' => $this->id_user,
            'id_creneau' => $this->id_creneau,
            'motif' => $this->motif,
            'statut' => $this->statut,
            'date_creation' => $this->date_creation ? $this->date_creation->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Crée une instance à partir d'un tableau de données
     * @param array $data Les données à utiliser pour créer l'instance
     * @return self Une nouvelle instance de RendezVous
     */
    public static function fromArray(array $data): self {
        $rdv = new self();
        if (isset($data['id'])) {
            $rdv->setId($data['id']);
        }
        if (isset($data['id_user'])) {
            $rdv->setIdUser($data['id_user']);
        }
        if (isset($data['id_creneau'])) {
            $rdv->setIdCreneau($data['id_creneau']);
        }
        if (isset($data['motif'])) {
            $rdv->setMotif($data['motif']);
        }
        if (isset($data['statut'])) {
            $rdv->setStatut($data['statut']);
        }
        if (isset($data['date_creation'])) {
            $rdv->setDateCreation(new \DateTime($data['date_creation']));
        }
        return $rdv;
    }
}