<?php
// Charge la configuration depuis le fichier .env à la racine du dépôt, hors du dossier public site/.
declare(strict_types=1);

/**
 * Arrête le script proprement en cas de configuration invalide :
 * détail dans les logs, et à l'écran seulement en local.
 */
function config_echec(string $detail): void
{
    error_log('[AI! FACTS] ' . $detail);
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    $local = ($_ENV['APP_ENV'] ?? '') === 'local';
    echo json_encode(['erreur' => $local ? $detail : 'Erreur interne. Réessaie plus tard.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    config_echec('Dépendances absentes : lancer « composer install » à la racine du dépôt.');
}
require_once $autoload;

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['APP_ENV', 'APP_URL'])->notEmpty();
    $dotenv->required('APP_ENV')->allowedValues(['local', 'production']);
} catch (Throwable $e) {
    config_echec($e->getMessage());
}

/**
 * Lit une variable d'environnement, avec une valeur par défaut.
 */
function env(string $cle, ?string $defaut = null): ?string
{
    $valeur = $_ENV[$cle] ?? '';
    return $valeur !== '' ? $valeur : $defaut;
}
