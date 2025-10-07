<?php

namespace App\Entities;

class Cabinet {
    private int $id;
    private string $nom;
    private string $adresse;
    private array $horaires = [];

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

    public function getNom(): string {
        return $this->nom;
    }

    public function getAdresse(): string {
        return $this->adresse;
    }

    public function getHoraires(): array {
        return $this->horaires;
    }

    // Setters
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setNom(string $nom): self {
        if (empty(trim($nom))) {
            throw new \InvalidArgumentException('Le nom du cabinet ne peut pas être vide');
        }
        if (strlen($nom) > 150) {
            throw new \InvalidArgumentException('Le nom du cabinet ne peut pas dépasser 150 caractères');
        }
        $this->nom = $nom;
        return $this;
    }

    public function setAdresse(string $adresse): self {
        if (empty(trim($adresse))) {
            throw new \InvalidArgumentException("L'adresse ne peut pas être vide");
        }
        if (strlen($adresse) > 255) {
            throw new \InvalidArgumentException("L'adresse ne peut pas dépasser 255 caractères");
        }
        $this->adresse = $adresse;
        return $this;
    }

    public function setHoraires(array $horaires): self {
        $this->horaires = $horaires;
        return $this;
    }

    /**
     * Ajoute un horaire au cabinet
     * @param string $jour Jour de la semaine
     * @param \DateTime $ouverture Heure d'ouverture
     * @param \DateTime $fermeture Heure de fermeture
     */
    public function ajouterHoraire(string $jour, \DateTime $ouverture, \DateTime $fermeture): self {
        $jourValides = ['LUNDI', 'MARDI', 'MERCREDI', 'JEUDI', 'VENDREDI', 'SAMEDI', 'DIMANCHE'];
        $jour = strtoupper($jour);
        
        if (!in_array($jour, $jourValides)) {
            throw new \InvalidArgumentException('Jour invalide');
        }

        if ($ouverture >= $fermeture) {
            throw new \InvalidArgumentException("L'heure d'ouverture doit être antérieure à l'heure de fermeture");
        }

        $this->horaires[$jour] = [
            'ouverture' => $ouverture,
            'fermeture' => $fermeture
        ];

        return $this;
    }

    /**
     * Vérifie si le cabinet est ouvert à une date donnée
     * @param \DateTime $date Date et heure à vérifier
     * @return bool
     */
    public function estOuvert(\DateTime $date): bool {
        $jour = strtoupper(strftime('%A', $date->getTimestamp()));
        
        if (!isset($this->horaires[$jour])) {
            return false;
        }

        $heure = clone $date;
        $heure->setDate(1, 1, 1); // Normalise la date pour comparer uniquement les heures

        return $heure >= $this->horaires[$jour]['ouverture'] 
            && $heure <= $this->horaires[$jour]['fermeture'];
    }

    /**
     * Récupère les horaires d'ouverture d'un jour spécifique
     * @param string $jour Jour de la semaine
     * @return array|null Tableau avec les heures d'ouverture et fermeture, ou null si fermé
     */
    public function getHorairesJour(string $jour): ?array {
        $jour = strtoupper($jour);
        return $this->horaires[$jour] ?? null;
    }
}