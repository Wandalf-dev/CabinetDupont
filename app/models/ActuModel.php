<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Entities\Article;
use App\Entities\User;
use PDO;

class ActuModel extends Model
{
    /**
     * Convertit un tableau (SQL) en entité Article
     */
    private function arrayToEntity(array $data): Article
    {
        // Gestion date_publication -> datePublication (DateTime)
        if (!empty($data['date_publication'])) {
            $data['datePublication'] = new \DateTime($data['date_publication']);
        }

        // Auteur si présent dans le SELECT
        $auteur = null;
        if (isset($data['auteur_id'], $data['auteur_nom'], $data['auteur_prenom'])) {
            $auteur = new User([
                'id'     => (int)$data['auteur_id'],
                'nom'    => (string)$data['auteur_nom'],
                'prenom' => (string)$data['auteur_prenom'],
            ]);
        }

        // Conversion snake_case -> camelCase pour l'entité
        $mapped = [];
        foreach ($data as $key => $value) {
            $camel = lcfirst(str_replace('_', '', ucwords((string)$key, '_')));
            $mapped[$camel] = $value;
        }

        $article = new Article($mapped);
        if ($auteur) {
            $article->setAuteur($auteur);
        }
        return $article;
    }

    /**
     * Convertit une entité Article en tableau (pour SQL)
     */
    private function entityToArray(Article $article): array
    {
        return [
            'id'               => $article->getId(),
            'auteur_id'        => $article->getAuteurId(),
            'titre'            => $article->getTitre(),
            'contenu'          => $article->getContenu(),
            'date_publication' => $article->getDatePublication()
                ? $article->getDatePublication()->format('Y-m-d H:i:s')
                : null,
            'statut'           => $article->getStatut(),
        ];
    }

    /**
     * Admin : toutes les actus (tous statuts)
     * @return Article[]
     */
    public function getAllActusAdmin(?int $limit = null): array
    {
        $sql = "SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
                FROM actualite a
                JOIN utilisateur u ON a.auteur_id = u.id
                ORDER BY a.date_publication DESC";
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();

        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $this->arrayToEntity($row);
        }
        return $out;
    }

    /**
     * Publique : actus publiées
     * @return Article[]
     */
    public function getAllActus(?int $limit = null): array
    {
        $sql = "SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
                FROM actualite a
                JOIN utilisateur u ON a.auteur_id = u.id
                WHERE a.statut = :statut
                ORDER BY a.date_publication DESC";
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':statut', Article::STATUT_PUBLIE);
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();

        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $this->arrayToEntity($row);
        }
        return $out;
    }

    /**
     * Publique : actus mises en avant (ici: mêmes critères, limitées)
     * @return Article[]
     */
    public function getFeaturedActus(int $limit = 3): array
    {
        $sql = "SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
                FROM actualite a
                JOIN utilisateur u ON a.auteur_id = u.id
                WHERE a.statut = :statut
                ORDER BY a.date_publication DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':statut', Article::STATUT_PUBLIE);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $this->arrayToEntity($row);
        }
        return $out;
    }

    public function getActuById(int $id): ?Article
    {
        $sql = "SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
                FROM actualite a
                JOIN utilisateur u ON a.auteur_id = u.id
                WHERE a.id = :id AND a.statut = :statut";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id'     => $id,
            ':statut' => Article::STATUT_PUBLIE,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->arrayToEntity($row) : null;
    }

    public function getActuByIdAdmin(int $id): ?Article
    {
        $sql = "SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
                FROM actualite a
                LEFT JOIN utilisateur u ON a.auteur_id = u.id
                WHERE a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            error_log("Aucune actualité trouvée pour l'ID: " . $id);
            return null;
        }
        
        error_log("Données de l'actualité: " . print_r($row, true));
        return $this->arrayToEntity($row);
    }

    /**
     * Recherche publique sur titre/contenu
     * @return Article[]
     */
    public function searchActus(string $keyword): array
    {
        $sql = "SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
                FROM actualite a
                JOIN utilisateur u ON a.auteur_id = u.id
                WHERE (a.titre LIKE :q OR a.contenu LIKE :q)
                  AND a.statut = :statut
                ORDER BY a.date_publication DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':q'      => '%'.$keyword.'%',
            ':statut' => Article::STATUT_PUBLIE,
        ]);

        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $this->arrayToEntity($row);
        }
        return $out;
    }

    public function createActu(Article $article): int
    {
        $data = $this->entityToArray($article);
        unset($data['id']); // auto-incrément

        $sql = "INSERT INTO actualite (auteur_id, titre, contenu, date_publication, statut)
                VALUES (:auteur_id, :titre, :contenu, :date_publication, :statut)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':auteur_id'        => $data['auteur_id'],
            ':titre'            => $data['titre'],
            ':contenu'          => $data['contenu'],
            ':date_publication' => $data['date_publication'],
            ':statut'           => $data['statut'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateActu(Article $article): bool
    {
        $data = $this->entityToArray($article);

        $sql = "UPDATE actualite
                SET titre = :titre,
                    contenu = :contenu,
                    date_publication = :date_publication,
                    statut = :statut
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titre'            => $data['titre'],
            ':contenu'          => $data['contenu'],
            ':date_publication' => $data['date_publication'],
            ':statut'           => $data['statut'],
            ':id'               => $data['id'],
        ]);
    }

    public function deleteActu(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM actualite WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
