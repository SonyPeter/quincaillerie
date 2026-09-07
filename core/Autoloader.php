<?php

/**
 * Autoloader PSR-4 simplifié
 * Charge automatiquement les classes depuis controllers/ et models/
 */

spl_autoload_register(function (string $className): void {
    $directories = [
        BASE_PATH . '/apps/controllers/',
        BASE_PATH . '/apps/models/',
        BASE_PATH . '/core/',
    ];

    foreach ($directories as $dir) {
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
