<?php
// Liens courts : redirige un chemin inconnu vers sa cible, d'après liens.json dans DATA_DIR (non versionné).
// Exemple de liens.json : {"proposer/fact01": "/proposer/?mtm_campaign=..."}
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$chemin = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$fichier = env('DATA_DIR', dirname(__DIR__) . '/data') . '/liens.json';
$liens = is_file($fichier) ? json_decode((string) file_get_contents($fichier), true) : [];
$cible = is_array($liens) ? ($liens[$chemin] ?? null) : null;

header('X-Robots-Tag: noindex');

// Cibles internes uniquement (pas de redirection ouverte vers un autre site)
if (is_string($cible) && str_starts_with($cible, '/') && !str_starts_with($cible, '//')) {
    header('Location: ' . $cible, true, 302);
    exit;
}

http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo 'Page introuvable.';
