<?php

namespace App\Entities;

class Service {
    private int $id;
    private int $cabinetId;
    private string $libelle;
    private int $dureeMinutes;
    private float $tarif;
    private ?string $description;
    private ?Cabinet $cabinet = null;

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getCabinetId(): int {
        return $this->cabinetId;
    }

    public function getLibelle(): string {
        return $this->libelle;
    }

    public function getDureeMinutes(): int {
        return $this->dureeMinutes;
    }

    public function getTarif(): float {
        return $this->tarif;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getCabinet(): ?Cabinet {
        return $this->cabinet;
    }

    // Setters
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setCabinetId(int $cabinetId): self {
        $this->cabinetId = $cabinetId;
        return $this;
    }

    public function setLibelle(string $libelle): self {
        if (empty(trim($libelle))) {
            throw new \InvalidArgumentException('Le libellé ne peut pas être vide');
        }
        if (strlen($libelle) > 120) {
            throw new \InvalidArgumentException('Le libellé ne peut pas dépasser 120 caractères');
        }
        $this->libelle = $libelle;
        return $this;
    }

    public function setDureeMinutes(int $dureeMinutes): self {
        if ($dureeMinutes <= 0) {
            throw new \InvalidArgumentException('La durée doit être supérieure à 0');
        }
        if ($dureeMinutes > 480) { // 8 heures max
            throw new \InvalidArgumentException('La durée ne peut pas dépasser 480 minutes');
        }
        $this->dureeMinutes = $dureeMinutes;
        return $this;
    }

    public function setTarif(float $tarif): self {
        if ($tarif < 0) {
            throw new \InvalidArgumentException('Le tarif ne peut pas être négatif');
        }
        $this->tarif = round($tarif, 2); // Arrondi à 2 décimales
        return $this;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function setCabinet(?Cabinet $cabinet): self {
        $this->cabinet = $cabinet;
        if ($cabinet) {
            $this->cabinetId = $cabinet->getId();
        }
        return $this;
    }

    // Méthodes utilitaires
    /**
     * Obtient la durée formatée (ex: "1h30" pour 90 minutes)
     */
    public function getDureeFormatee(): string {
        $heures = floor($this->dureeMinutes / 60);
        $minutes = $this->dureeMinutes % 60;
        
        if ($heures > 0) {
            return $minutes > 0 ? "${heures}h${minutes}" : "${heures}h";
        }
        return "${minutes}min";
    }

    /**
     * Obtient le tarif formaté avec le symbole €
     */
    public function getTarifFormate(): string {
        return number_format($this->tarif, 2, ',', ' ') . ' €';
    }

    /**
     * Calcule l'heure de fin en fonction d'une heure de début
     * @param \DateTime $debut Heure de début
     * @return \DateTime Heure de fin
     */
    public function calculerHeureFin(\DateTime $debut): \DateTime {
        $fin = clone $debut;
        $fin->modify("+{$this->dureeMinutes} minutes");
        return $fin;
    }
}