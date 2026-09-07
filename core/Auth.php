<?php

/**
 * Contrôle d'accès : session et rôle (admin / vendeur)
 */

class Auth
{
    /**
     * Route sur laquelle l'utilisateur peut toujours arriver, même s'il
     * doit changer son mot de passe temporaire (sinon boucle infinie)
     */
    private const ROUTE_COMPTE = '/utilisateurs';

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['must_change_password'] = !empty($user['must_change_password']);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Redirige vers /login si l'utilisateur n'est pas connecté, ou vers la
     * page Comptes s'il doit d'abord changer son mot de passe temporaire
     */
    public static function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (!empty($_SESSION['must_change_password']) && self::currentPath() !== self::ROUTE_COMPTE) {
            header('Location: ' . BASE_URL . self::ROUTE_COMPTE);
            exit;
        }
    }

    /**
     * Vérifie que l'utilisateur est connecté ET possède un des rôles autorisés
     */
    public static function requireRole(array $roles): void
    {
        self::requireLogin();

        if (!in_array($_SESSION['user_role'], $roles, true)) {
            http_response_code(403);
            echo '<h1>403 — Accès refusé</h1><p>Vous n\'avez pas les droits nécessaires pour accéder à cette page.</p>';
            exit;
        }
    }

    public static function isAdmin(): bool
    {
        return ($_SESSION['user_role'] ?? null) === 'admin';
    }

    public static function mustChangePassword(): bool
    {
        return !empty($_SESSION['must_change_password']);
    }

    private static function currentPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        return $path;
    }
}
