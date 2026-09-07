<?php

/**
 * Authentification : connexion, déconnexion
 */

class authController
{
    /**
     * Affiche le formulaire de connexion (GET /login)
     */
    public function showLogin(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $csrfToken = generateCsrfToken();
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        require BASE_PATH . '/apps/views/auth/login.php';
    }

    /**
     * Traite la soumission du formulaire de connexion (POST /login)
     */
    public function login(): void
    {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['login_error'] = 'Requête invalide, veuillez réessayer.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $_SESSION['login_error'] = 'Veuillez remplir tous les champs.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $user = (new userModel())->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Identifiant ou mot de passe incorrect.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        Auth::login($user);
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    /**
     * Déconnecte l'utilisateur (POST /logout)
     */
    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
