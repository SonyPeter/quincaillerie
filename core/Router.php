<?php

/**
 * Routeur HTTP simple : enregistre des routes GET/POST
 * et dispatche vers le bon contrôleur@méthode
 */

class Router
{
    private array $routes = [];

    /**
     * Enregistre une route GET
     */
    public function get(string $path, string $target): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'path'   => $path,
            'target' => $target,
        ];
    }

    /**
     * Enregistre une route POST
     */
    public function post(string $path, string $target): void
    {
        $this->routes[] = [
            'method' => 'POST',
            'path'   => $path,
            'target' => $target,
        ];
    }

    /**
     * Analyse l'URL courante et appelle le bon contrôleur@méthode.
     * Fonctionne sous WAMP en sous-dossier (ex: /quicaillerie/)
     * comme sur un vhost à la racine.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $fullUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

        if ($basePath !== '' && str_starts_with($fullUri, $basePath)) {
            $uri = substr($fullUri, strlen($basePath));
        } else {
            $uri = $fullUri;
        }

        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                [$controllerName, $actionName] = explode('@', $route['target']);

                if (!class_exists($controllerName)) {
                    $this->abort(500, "Contrôleur introuvable : $controllerName");
                }

                $controller = new $controllerName();

                if (!method_exists($controller, $actionName)) {
                    $this->abort(500, "Méthode introuvable : $actionName");
                }

                $controller->$actionName();
                return;
            }
        }

        $this->abort(404, "Page introuvable — URI : " . htmlspecialchars($uri));
    }

    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        echo "<h1>Erreur $code</h1><p>" . htmlspecialchars($message) . "</p>";
        exit;
    }
}
