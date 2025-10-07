<?php

namespace App\Entities;

class User {
    private int $id;
    private string $role;
    private string $nom;
    private string $prenom;
    private string $email;
    private ?string $telephone;
    private ?string $avatar;
    private string $passwordHash;
    private \DateTime $dateInscription;
    private ?\DateTime $dateNaissance;

    // Constantes pour les rôles
    public const ROLE_PATIENT = 'PATIENT';
    public const ROLE_MEDECIN = 'MEDECIN';
    public const ROLE_SECRETAIRE = 'SECRETAIRE';

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

    public function getRole(): string {
        return $this->role;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getTelephone(): ?string {
        return $this->telephone;
    }

    public function getAvatar(): ?string {
        return $this->avatar;
    }

    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    public function getDateInscription(): \DateTime {
        return $this->dateInscription;
    }

    public function getDateNaissance(): ?\DateTime {
        return $this->dateNaissance;
    }

    // Setters
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setRole(string $role): self {
        if (!in_array($role, [self::ROLE_PATIENT, self::ROLE_MEDECIN, self::ROLE_SECRETAIRE])) {
            throw new \InvalidArgumentException('Rôle invalide');
        }
        $this->role = $role;
        return $this;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;
        return $this;
    }

    public function setPrenom(string $prenom): self {
        $this->prenom = $prenom;
        return $this;
    }

    public function setEmail(string $email): self {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email invalide');
        }
        $this->email = $email;
        return $this;
    }

    public function setTelephone(?string $telephone): self {
        $this->telephone = $telephone;
        return $this;
    }

    public function setAvatar(?string $avatar): self {
        $this->avatar = $avatar;
        return $this;
    }

    public function setPasswordHash(string $passwordHash): self {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function setDateInscription(\DateTime $dateInscription): self {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    public function setDateNaissance(?\DateTime $dateNaissance): self {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    // Méthodes utilitaires
    public function getNomComplet(): string {
        return "$this->prenom $this->nom";
    }

    public function isPatient(): bool {
        return $this->role === self::ROLE_PATIENT;
    }

    public function isMedecin(): bool {
        return $this->role === self::ROLE_MEDECIN;
    }

    public function isSecretaire(): bool {
        return $this->role === self::ROLE_SECRETAIRE;
    }
}