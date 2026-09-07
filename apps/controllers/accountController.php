<?php

/**
 * accountController — "Comptes" : profil personnel (tous rôles) + gestion
 * des comptes vendeurs par l'admin (liste, création avec mot de passe
 * temporaire à changer obligatoirement)
 */

class accountController extends DashboardPage
{
    public function index(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        $userModel = new userModel();

        $data = [
            'csrfToken' => generateCsrfToken(),
            'moi' => $userModel->findById($_SESSION['user_id']),
        ];

        if (Auth::isAdmin()) {
            $data['vendeurs'] = $userModel->allVendeurs();
        }

        $this->render(BASE_PATH . '/apps/views/dashboard/comptes.php', $data);
    }

    public function store(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->rediriger('Requête invalide, veuillez réessayer.', true);
        }

        $action = $_POST['action'] ?? '';

        match ($action) {
            'modifier_profil' => $this->modifierProfil(),
            'changer_mot_de_passe' => $this->changerMotDePasse(),
            'ajouter_vendeur' => $this->ajouterVendeur(),
            default => $this->rediriger('Action inconnue.', true),
        };
    }

    private function modifierProfil(): void
    {
        $nom = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['phone'] ?? '');

        if ($nom === '' || $email === '') {
            $this->rediriger('Le nom et l\'email sont obligatoires.', true);
        }

        try {
            (new userModel())->updateProfile($_SESSION['user_id'], $nom, $email, $telephone);
            $_SESSION['user_name'] = $nom;
            $this->rediriger('Profil mis à jour.');
        } catch (PDOException $e) {
            $this->rediriger('Cet email est déjà utilisé par un autre compte.', true);
        }
    }

    private function changerMotDePasse(): void
    {
        $model = new userModel();
        $moi = $model->findById($_SESSION['user_id']);

        $actuel = $_POST['current_password'] ?? '';
        $nouveau = $_POST['new_password'] ?? '';
        $confirmation = $_POST['confirm_password'] ?? '';

        if (!password_verify($actuel, $moi['password'])) {
            $this->rediriger('Mot de passe actuel incorrect.', true);
        }
        if (strlen($nouveau) < 6) {
            $this->rediriger('Le nouveau mot de passe doit contenir au moins 6 caractères.', true);
        }
        if ($nouveau !== $confirmation) {
            $this->rediriger('La confirmation ne correspond pas au nouveau mot de passe.', true);
        }

        $model->updatePassword($_SESSION['user_id'], $nouveau);
        $_SESSION['must_change_password'] = false;
        $this->rediriger('Mot de passe modifié avec succès.');
    }

    private function ajouterVendeur(): void
    {
        Auth::requireRole(['admin']);

        $nom = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['phone'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $motDePasse = $_POST['password'] ?? '';

        if ($nom === '' || $email === '' || $username === '' || $motDePasse === '') {
            $this->rediriger('Tous les champs obligatoires doivent être remplis.', true);
        }
        if (strlen($motDePasse) < 6) {
            $this->rediriger('Le mot de passe temporaire doit contenir au moins 6 caractères.', true);
        }

        try {
            (new userModel())->createVendeur([
                'name' => $nom,
                'email' => $email,
                'phone' => $telephone,
                'username' => $username,
                'password' => $motDePasse,
            ], $_SESSION['user_id']);

            $this->rediriger("Vendeur créé. Identifiant : $username — Mot de passe temporaire : $motDePasse (il devra le changer à la première connexion).");
        } catch (PDOException $e) {
            $this->rediriger('Cet identifiant ou cet email est déjà utilisé.', true);
        }
    }

    private function rediriger(string $message, bool $erreur = false): void
    {
        $_SESSION[$erreur ? 'flash_error' : 'flash_success'] = $message;
        header('Location: ' . BASE_URL . '/utilisateurs');
        exit;
    }
}
