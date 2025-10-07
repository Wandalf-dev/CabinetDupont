<?php

namespace App\Entities;

class Article {
    private ?int $id = null;
    private ?int $auteurId = null;
    private string $titre = '';
    private string $contenu = '';
    private ?\DateTime $datePublication = null;
    private string $statut = self::STATUT_BROUILLON;

    // Constantes pour les statuts
    public const STATUT_BROUILLON = 'BROUILLON';
    public const STATUT_PUBLIE = 'PUBLIE';
    public const STATUT_ARCHIVE = 'ARCHIVE';

    // Propriétés calculées
    private ?string $auteurNom = null;
    private ?string $auteurPrenom = null;
    private ?User $auteur = null;

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getAuteurId(): ?int {
        return $this->auteurId;
    }

    public function getTitre(): string {
        return $this->titre;
    }

    public function getContenu(): string {
        return $this->contenu;
    }

    public function getDatePublication(): ?\DateTime {
        return $this->datePublication;
    }

    public function getStatut(): string {
        return $this->statut;
    }

    public function getAuteur(): ?User {
        return $this->auteur;
    }

    public function getAuteurNom(): ?string {
        return $this->auteurNom;
    }

    public function getAuteurPrenom(): ?string {
        return $this->auteurPrenom;
    }

    // Setters
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setAuteurId(int $auteurId): self {
        $this->auteurId = $auteurId;
        return $this;
    }

    public function setTitre(string $titre): self {
        if (empty(trim($titre))) {
            throw new \InvalidArgumentException('Le titre ne peut pas être vide');
        }
        if (strlen($titre) > 200) {
            throw new \InvalidArgumentException('Le titre ne peut pas dépasser 200 caractères');
        }
        $this->titre = $titre;
        return $this;
    }

    public function setContenu(string $contenu): self {
        if (empty(trim($contenu))) {
            throw new \InvalidArgumentException('Le contenu ne peut pas être vide');
        }
        $this->contenu = $contenu;
        return $this;
    }

    public function setDatePublication(?\DateTime $datePublication): self {
        $this->datePublication = $datePublication;
        return $this;
    }

    public function setStatut(string $statut): self {
        if (!in_array($statut, [self::STATUT_BROUILLON, self::STATUT_PUBLIE, self::STATUT_ARCHIVE])) {
            throw new \InvalidArgumentException('Statut invalide');
        }
        $this->statut = $statut;
        return $this;
    }

    public function setAuteur(?User $auteur): self {
        $this->auteur = $auteur;
        if ($auteur) {
            $this->auteurNom = $auteur->getNom();
            $this->auteurPrenom = $auteur->getPrenom();
            $this->auteurId = $auteur->getId();
        }
        return $this;
    }

    public function setAuteurNom(?string $nom): self {
        $this->auteurNom = $nom;
        return $this;
    }

    public function setAuteurPrenom(?string $prenom): self {
        $this->auteurPrenom = $prenom;
        return $this;
    }

    // Méthodes utilitaires
    public function isPublie(): bool {
        return $this->statut === self::STATUT_PUBLIE;
    }

    public function isBrouillon(): bool {
        return $this->statut === self::STATUT_BROUILLON;
    }

    public function isArchive(): bool {
        return $this->statut === self::STATUT_ARCHIVE;
    }

    public function getAuteurNomComplet(): string {
        if ($this->auteur) {
            return $this->auteur->getNomComplet();
        }
        return $this->auteurPrenom . ' ' . $this->auteurNom;
    }

    public function getExtrait(int $longueur = 150): string {
        $texte = strip_tags($this->contenu);
        if (strlen($texte) <= $longueur) {
            return $texte;
        }
        return substr($texte, 0, strrpos(substr($texte, 0, $longueur), ' ')) . '...';
    }
}