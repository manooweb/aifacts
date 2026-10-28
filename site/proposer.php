<?php
// Reçoit les propositions de facts envoyées par le formulaire d'aifacts.fr.
// Les propositions sont ajoutées à un fichier JSON Lines hors du dossier public, puis notifiées par e-mail.
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex');

require_once dirname(__DIR__) . '/bootstrap.php';

function repondre(int $code, array $donnees): void
{
    http_response_code($code);
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    exit;
}

$dossier = env('DATA_DIR', dirname(__DIR__) . '/data');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    repondre(405, ['erreur' => 'Méthode non autorisée.']);
}
if (($_SERVER['HTTP_ORIGIN'] ?? '') !== env('APP_URL')) {
    repondre(403, ['erreur' => 'Origine non autorisée.']);
}

// Anti-spam : champ piège invisible et délai minimum entre l'affichage et l'envoi
$depuis = time() - (int) ($_POST['t'] ?? 0);
if (($_POST['site_web'] ?? '') !== '' || $depuis < 3 || $depuis > 86400) {
    repondre(200, ['ok' => true]); // On ne dit rien au robot
}

$texte = fn(string $cle): string => trim((string) preg_replace('/\s+/u', ' ', (string) ($_POST[$cle] ?? '')));
$setup = $texte('setup');
$punchline = $texte('punchline');
$author = $texte('author');

if ($setup === '' || $punchline === '') {
    repondre(422, ['erreur' => 'L\'accroche et la chute sont obligatoires.']);
}
if (mb_strlen($setup) > 160 || mb_strlen($punchline) > 160 || mb_strlen($author) > 60) {
    repondre(422, ['erreur' => 'Un fact, c\'est court : 160 caractères maximum par partie.']);
}
if (($_POST['regles'] ?? '') !== 'oui') {
    repondre(422, ['erreur' => 'Merci d\'accepter les règles de publication.']);
}

if (!is_dir($dossier) && !mkdir($dossier, 0700, true)) {
    repondre(500, ['erreur' => 'Enregistrement impossible pour le moment.']);
}

// Limite : 5 propositions par heure et par visiteur. L'IP n'est jamais stockée, seulement une empreinte salée.
$fichierSel = $dossier . '/sel.txt';
if (!is_file($fichierSel)) {
    file_put_contents($fichierSel, bin2hex(random_bytes(16)));
}
$empreinte = hash_hmac('sha256', $_SERVER['REMOTE_ADDR'] ?? '', (string) file_get_contents($fichierSel));

$fichierLimites = $dossier . '/limites.json';
$f = fopen($fichierLimites, 'c+');
flock($f, LOCK_EX);
$limites = json_decode((string) stream_get_contents($f), true) ?: [];
$limites = array_filter(
    array_map(fn(array $dates) => array_values(array_filter($dates, fn(int $d) => $d > time() - 3600)), $limites)
);
if (count($limites[$empreinte] ?? []) >= 5) {
    flock($f, LOCK_UN);
    fclose($f);
    repondre(429, ['erreur' => 'Doucement ! Réessaie dans une heure.']);
}
$limites[$empreinte][] = time();
ftruncate($f, 0);
rewind($f);
fwrite($f, json_encode($limites));
flock($f, LOCK_UN);
fclose($f);

$proposition = [
    'date' => date('c'),
    'setup' => $setup,
    'punchline' => $punchline,
    'author' => $author,
    'status' => 'propose',
];
$ligne = json_encode($proposition, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
if (file_put_contents($dossier . '/propositions.jsonl', $ligne, FILE_APPEND | LOCK_EX) === false) {
    repondre(500, ['erreur' => 'Enregistrement impossible pour le moment.']);
}

if (env('NOTIFY_EMAIL') !== null) {
    $corps = "Accroche : $setup\nChute : $punchline\nAuteur : " . ($author !== '' ? $author : '(anonyme)') . "\n";
    mail(
        env('NOTIFY_EMAIL'),
        mb_encode_mimeheader('[AI! FACTS] Nouvelle proposition', 'UTF-8'),
        $corps,
        'From: AI! FACTS <' . env('MAIL_FROM', 'no-reply@aifacts.fr') . ">\r\nContent-Type: text/plain; charset=UTF-8"
    );
}

repondre(201, ['ok' => true]);
