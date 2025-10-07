<?php

namespace App\Entities;

/**
 * Classe Notification
 * 
 * Cette classe représente une notification envoyée à un utilisateur.
 * Elle peut être liée à un rendez-vous ou être une notification système.
 * La classe gère également l'état de lecture et les horodatages associés.
 */
class Notification {
    /** @var int|null Identifiant unique de la notification */
    private ?int $id = null;

    /** @var int Identifiant de l'utilisateur destinataire */
    private int $id_user;

    /** @var int|null Identifiant du rendez-vous associé (optionnel) */
    private ?int $id_rendezvous;

    /** @var string Type de notification (rdv_confirme, rdv_annule, rdv_rappel, systeme) */
    private string $type;

    /** @var string Message de la notification */
    private string $message;

    /** @var bool Indique si la notification a été lue */
    private bool $lu;

    /** @var \DateTime Date de création de la notification */
    private \DateTime $date_creation;

    /** @var \DateTime|null Date de lecture de la notification */
    private ?\DateTime $date_lecture;

    /**
     * Constructeur de la classe Notification
     * Initialise une nouvelle notification avec la date actuelle et comme non lue
     */
    public function __construct() {
        $this->date_creation = new \DateTime();
        $this->lu = false;
        $this->date_lecture = null;
    }

    /**
     * Récupère l'identifiant de la notification
     * @return int|null L'identifiant de la notification ou null si non défini
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Définit l'identifiant de la notification
     * @param int|null $id L'identifiant à définir
     * @return self
     */
    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    /**
     * Récupère l'identifiant de l'utilisateur destinataire
     * @return int L'identifiant de l'utilisateur
     */
    public function getIdUser(): int {
        return $this->id_user;
    }

    /**
     * Définit l'identifiant de l'utilisateur destinataire
     * @param int $id_user L'identifiant de l'utilisateur
     * @return self
     */
    public function setIdUser(int $id_user): self {
        $this->id_user = $id_user;
        return $this;
    }

    /**
     * Récupère l'identifiant du rendez-vous associé
     * @return int|null L'identifiant du rendez-vous ou null si non associé
     */
    public function getIdRendezVous(): ?int {
        return $this->id_rendezvous;
    }

    /**
     * Définit l'identifiant du rendez-vous associé
     * @param int|null $id_rendezvous L'identifiant du rendez-vous
     * @return self
     */
    public function setIdRendezVous(?int $id_rendezvous): self {
        $this->id_rendezvous = $id_rendezvous;
        return $this;
    }

    /**
     * Récupère le type de la notification
     * @return string Le type de notification
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * Définit le type de la notification
     * @param string $type Le type de notification (rdv_confirme, rdv_annule, rdv_rappel, systeme)
     * @return self
     * @throws \InvalidArgumentException Si le type n'est pas valide
     */
    public function setType(string $type): self {
        $types_valides = ['rdv_confirme', 'rdv_annule', 'rdv_rappel', 'systeme'];
        if (!in_array($type, $types_valides)) {
            throw new \InvalidArgumentException("Type de notification non valide");
        }
        $this->type = $type;
        return $this;
    }

    /**
     * Récupère le message de la notification
     * @return string Le message
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * Définit le message de la notification
     * @param string $message Le message à définir
     * @return self
     */
    public function setMessage(string $message): self {
        $this->message = $message;
        return $this;
    }

    /**
     * Vérifie si la notification a été lue
     * @return bool true si la notification a été lue, false sinon
     */
    public function isLu(): bool {
        return $this->lu;
    }

    /**
     * Définit l'état de lecture de la notification
     * Met également à jour la date de lecture si la notification est marquée comme lue
     * @param bool $lu L'état de lecture à définir
     * @return self
     */
    public function setLu(bool $lu): self {
        $this->lu = $lu;
        if ($lu && $this->date_lecture === null) {
            $this->date_lecture = new \DateTime();
        }
        return $this;
    }

    /**
     * Récupère la date de création de la notification
     * @return \DateTime La date de création
     */
    public function getDateCreation(): \DateTime {
        return $this->date_creation;
    }

    /**
     * Définit la date de création de la notification
     * @param \DateTime $date_creation La date de création à définir
     * @return self
     */
    public function setDateCreation(\DateTime $date_creation): self {
        $this->date_creation = $date_creation;
        return $this;
    }

    /**
     * Récupère la date de lecture de la notification
     * @return \DateTime|null La date de lecture ou null si non lue
     */
    public function getDateLecture(): ?\DateTime {
        return $this->date_lecture;
    }

    /**
     * Définit la date de lecture de la notification
     * @param \DateTime|null $date_lecture La date de lecture à définir
     * @return self
     */
    public function setDateLecture(?\DateTime $date_lecture): self {
        $this->date_lecture = $date_lecture;
        return $this;
    }

    /**
     * Convertit l'instance en tableau associatif
     * @return array Les données de la notification sous forme de tableau
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'id_user' => $this->id_user,
            'id_rendezvous' => $this->id_rendezvous,
            'type' => $this->type,
            'message' => $this->message,
            'lu' => $this->lu,
            'date_creation' => $this->date_creation->format('Y-m-d H:i:s'),
            'date_lecture' => $this->date_lecture ? $this->date_lecture->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Crée une instance à partir d'un tableau de données
     * @param array $data Les données à utiliser pour créer l'instance
     * @return self Une nouvelle instance de Notification
     */
    public static function fromArray(array $data): self {
        $notification = new self();
        if (isset($data['id'])) {
            $notification->setId($data['id']);
        }
        if (isset($data['id_user'])) {
            $notification->setIdUser($data['id_user']);
        }
        if (isset($data['id_rendezvous'])) {
            $notification->setIdRendezVous($data['id_rendezvous']);
        }
        if (isset($data['type'])) {
            $notification->setType($data['type']);
        }
        if (isset($data['message'])) {
            $notification->setMessage($data['message']);
        }
        if (isset($data['lu'])) {
            $notification->setLu((bool) $data['lu']);
        }
        if (isset($data['date_creation'])) {
            $notification->setDateCreation(new \DateTime($data['date_creation']));
        }
        if (isset($data['date_lecture'])) {
            $notification->setDateLecture($data['date_lecture'] ? new \DateTime($data['date_lecture']) : null);
        }
        return $notification;
    }
}