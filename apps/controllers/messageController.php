<?php

/**
 * messageController — Messagerie interne : n'importe quel utilisateur peut
 * écrire à n'importe quel autre utilisateur, ou à tout le monde
 */

class messageController extends DashboardPage
{
    public function index(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        $messageModel = new messageModel();
        $userId = $_SESSION['user_id'];

        $data = [
            'csrfToken' => generateCsrfToken(),
            'reception' => $messageModel->boiteReception($userId),
            'envoyes' => $messageModel->messagesEnvoyes($userId),
            'destinataires' => (new userModel())->allExcept($userId),
        ];

        // La boîte de réception vient d'être ouverte : la notification flottante se vide
        $messageModel->toutMarquerLu($userId);

        $this->render(BASE_PATH . '/apps/views/dashboard/messages.php', $data);
    }

    public function store(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->rediriger('Requête invalide, veuillez réessayer.', true);
        }

        $destinataire = trim($_POST['recipient_id'] ?? '');
        $contenu = trim($_POST['content'] ?? '');

        if ($contenu === '') {
            $this->rediriger('Le message ne peut pas être vide.', true);
        }

        $recipientId = $destinataire === '' ? null : $destinataire;

        (new messageModel())->envoyer($_SESSION['user_id'], $recipientId, $contenu);

        $this->rediriger($recipientId === null ? 'Message envoyé à tout le monde.' : 'Message envoyé.');
    }

    private function rediriger(string $message, bool $erreur = false): void
    {
        $_SESSION[$erreur ? 'flash_error' : 'flash_success'] = $message;
        header('Location: ' . BASE_URL . '/messages');
        exit;
    }
}
