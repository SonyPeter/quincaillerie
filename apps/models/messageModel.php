<?php

/**
 * Modèle Message — messagerie interne (admin <-> vendeur, dans les deux
 * sens), avec envoi à une personne précise ou à tout le monde
 * (recipient_id NULL), et suivi de lecture par destinataire
 */

class messageModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function envoyer(string $senderId, ?string $recipientId, string $content): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO messages (id, sender_id, recipient_id, content)
             VALUES (:id, :sender_id, :recipient_id, :content)'
        );
        $stmt->execute([
            'id' => Uuid::v4(),
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'content' => $content,
        ]);
    }

    /**
     * Messages reçus par cet utilisateur : ceux qui lui sont adressés
     * directement, plus les diffusions à tout le monde (sauf les siennes)
     */
    public function boiteReception(string $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.name AS sender_name,
                    (mr.user_id IS NOT NULL) AS is_read
             FROM messages m
             JOIN users u ON u.id = m.sender_id
             LEFT JOIN message_reads mr ON mr.message_id = m.id AND mr.user_id = :user_id1
             WHERE (m.recipient_id = :user_id2 OR m.recipient_id IS NULL)
               AND m.sender_id != :user_id3
             ORDER BY m.created_at DESC"
        );
        $stmt->execute(['user_id1' => $userId, 'user_id2' => $userId, 'user_id3' => $userId]);

        return $stmt->fetchAll();
    }

    /**
     * Messages envoyés par cet utilisateur
     */
    public function messagesEnvoyes(string $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.name AS recipient_name
             FROM messages m
             LEFT JOIN users u ON u.id = m.recipient_id
             WHERE m.sender_id = :user_id
             ORDER BY m.created_at DESC"
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function nombreNonLus(string $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*)
             FROM messages m
             LEFT JOIN message_reads mr ON mr.message_id = m.id AND mr.user_id = :user_id1
             WHERE (m.recipient_id = :user_id2 OR m.recipient_id IS NULL)
               AND m.sender_id != :user_id3
               AND mr.user_id IS NULL"
        );
        $stmt->execute(['user_id1' => $userId, 'user_id2' => $userId, 'user_id3' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Marque comme lus, pour cet utilisateur, tous les messages actuellement
     * non lus de sa boîte de réception (appelé à l'ouverture de la page Messages)
     */
    public function toutMarquerLu(string $userId): void
    {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO message_reads (message_id, user_id)
             SELECT m.id, :user_id1
             FROM messages m
             LEFT JOIN message_reads mr ON mr.message_id = m.id AND mr.user_id = :user_id2
             WHERE (m.recipient_id = :user_id3 OR m.recipient_id IS NULL)
               AND m.sender_id != :user_id4
               AND mr.user_id IS NULL"
        );
        $stmt->execute([
            'user_id1' => $userId,
            'user_id2' => $userId,
            'user_id3' => $userId,
            'user_id4' => $userId,
        ]);
    }
}
