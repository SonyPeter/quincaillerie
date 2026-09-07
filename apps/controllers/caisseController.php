<?php

/**
 * caisseController — Caisse (soldes par moyen de paiement, conversion
 * NatCash/MonCash vers Cash) et retraits (demande par un vendeur,
 * validation ou refus par l'admin)
 */

class caisseController extends DashboardPage
{
    public function index(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        $model = new caisseModel();

        $data = [
            'csrfToken' => generateCsrfToken(),
        ];

        if (Auth::isAdmin()) {
            $data['soldes'] = $model->soldes();
            $data['demandesEnAttente'] = $model->demandes('en_attente');
            $data['historique'] = $model->demandes();
        } else {
            $data['mesDemandes'] = $model->demandes(null, $_SESSION['user_id']);
        }

        $this->render(BASE_PATH . '/apps/views/dashboard/caisse.php', $data);
    }

    public function store(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirectAvecErreur('Requête invalide, veuillez réessayer.');
        }

        $action = $_POST['action'] ?? '';

        match ($action) {
            'convertir' => $this->convertir(),
            'demander_retrait' => $this->demanderRetrait(),
            'valider_retrait' => $this->resoudreRetrait('valide'),
            'refuser_retrait' => $this->resoudreRetrait('refuse'),
            default => $this->redirectAvecErreur('Action inconnue.'),
        };
    }

    private function convertir(): void
    {
        Auth::requireRole(['admin']);

        $model = new caisseModel();

        try {
            $model->convertir(
                (string) ($_POST['from_method'] ?? ''),
                (float) ($_POST['amount'] ?? 0),
                $_SESSION['user_id']
            );
            $this->redirectAvecSucces('Conversion effectuée avec succès.');
        } catch (InvalidArgumentException $e) {
            $this->redirectAvecErreur($e->getMessage());
        }
    }

    private function demanderRetrait(): void
    {
        $model = new caisseModel();

        try {
            $model->creerDemande(
                (string) ($_POST['type'] ?? ''),
                (float) ($_POST['amount'] ?? 0),
                (string) ($_POST['description'] ?? ''),
                $_SESSION['user_id']
            );
            $this->redirectAvecSucces('Demande de retrait envoyée. En attente de validation.');
        } catch (InvalidArgumentException $e) {
            $this->redirectAvecErreur($e->getMessage());
        }
    }

    private function resoudreRetrait(string $statut): void
    {
        Auth::requireRole(['admin']);

        $model = new caisseModel();
        $model->resoudre((string) ($_POST['id'] ?? ''), $statut, $_SESSION['user_id']);

        $this->redirectAvecSucces($statut === 'valide' ? 'Retrait validé.' : 'Retrait refusé.');
    }

    private function redirectAvecSucces(string $message): void
    {
        $_SESSION['flash_success'] = $message;
        header('Location: ' . BASE_URL . '/caisse');
        exit;
    }

    private function redirectAvecErreur(string $message): void
    {
        $_SESSION['flash_error'] = $message;
        header('Location: ' . BASE_URL . '/caisse');
        exit;
    }
}
