<?php
/**
 * contact.php — Traitement du formulaire de contact BM2R
 * ------------------------------------------------------
 * Reçoit la demande en POST (AJAX), vérifie honeypot + reCAPTCHA v3,
 * puis envoie 2 mails via SMTP authentifié (PHPMailer) :
 *   1. la demande -> bm2rbatimulti@gmail.com (Reply-To = email du prospect)
 *   2. un accusé de réception -> le prospect (si email fourni)
 * Répond en JSON : {"ok":true} ou {"ok":false,"error":"..."}.
 */

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/lib/PHPMailer/Exception.php';
require __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/** Réponse JSON + arrêt. */
function respond($ok, $error = null)
{
    echo json_encode(['ok' => $ok, 'error' => $error], JSON_UNESCAPED_UNICODE);
    exit;
}

/** Nettoie une valeur de champ texte. */
function field($key, $maxlen = 2000)
{
    $v = isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
    $v = str_replace(["\r", "\0"], '', $v);
    return mb_substr($v, 0, $maxlen);
}

// --- Méthode ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, 'Méthode non autorisée.');
}

// --- Config ----------------------------------------------------------------
$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    respond(false, 'Configuration serveur manquante.');
}
$cfg = require $configFile;

// --- Honeypot (champ piège invisible : doit rester vide) -------------------
if (field('website') !== '') {
    // Bot probable : on simule un succès pour ne pas l'informer.
    respond(true);
}

// --- Champs ----------------------------------------------------------------
$nom     = field('nom', 120);
$tel     = field('tel', 40);
$email   = field('email', 160);
$commune = field('commune', 120);
$sujet   = field('sujet', 120);
$message = field('message', 5000);
$consent = isset($_POST['consent']);

// --- Validation ------------------------------------------------------------
$errors = [];
if ($nom === '')                                   { $errors[] = 'le nom'; }
if ($tel === '')                                   { $errors[] = 'le téléphone'; }
if ($sujet === '')                                 { $errors[] = 'le type de projet'; }
if ($message === '')                               { $errors[] = 'le message'; }
if (!$consent)                                     { $errors[] = 'le consentement'; }
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, "L'adresse email saisie n'est pas valide.");
}
if ($errors) {
    respond(false, 'Merci de renseigner : ' . implode(', ', $errors) . '.');
}

// --- reCAPTCHA v3 (ignoré si clé secrète non configurée) -------------------
$secret = isset($cfg['recaptcha_secret']) ? $cfg['recaptcha_secret'] : '';
if ($secret !== '' && $secret !== 'CLE_SECRETE_RECAPTCHA') {
    $token = field('recaptcha_token', 4000);
    if ($token === '') {
        respond(false, 'Échec de la vérification anti-spam. Rechargez la page et réessayez.');
    }
    $verify = @file_get_contents(
        'https://www.google.com/recaptcha/api/siteverify',
        false,
        stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'secret'   => $secret,
                    'response' => $token,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ]),
                'timeout' => 8,
            ],
        ])
    );
    $res = $verify ? json_decode($verify, true) : null;
    $minScore = isset($cfg['recaptcha_min_score']) ? (float) $cfg['recaptcha_min_score'] : 0.5;
    if (!$res || empty($res['success']) || (isset($res['score']) && $res['score'] < $minScore)) {
        respond(false, "Votre demande a été bloquée par la protection anti-spam. Vous pouvez nous appeler au 06 10 03 34 08.");
    }
}

// --- Construction des mails ------------------------------------------------
$dateFr = date('d/m/Y à H:i');

// 1) Notification interne (vers Francis)
$adminText =
    "Nouvelle demande de devis depuis bm2rbatimulti.fr\n" .
    "------------------------------------------------\n" .
    "Nom       : $nom\n" .
    "Téléphone : $tel\n" .
    "Email     : " . ($email !== '' ? $email : '(non renseigné)') . "\n" .
    "Commune   : " . ($commune !== '' ? $commune : '(non renseignée)') . "\n" .
    "Projet    : $sujet\n" .
    "Reçu le   : $dateFr\n\n" .
    "Message :\n$message\n";

$esc = fn($s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
$adminHtml =
    '<h2 style="margin:0 0 12px;font-family:Arial,sans-serif">Nouvelle demande de devis</h2>' .
    '<table style="font-family:Arial,sans-serif;font-size:14px;border-collapse:collapse">' .
    '<tr><td style="padding:4px 12px 4px 0;color:#666">Nom</td><td><b>' . $esc($nom) . '</b></td></tr>' .
    '<tr><td style="padding:4px 12px 4px 0;color:#666">Téléphone</td><td><b>' . $esc($tel) . '</b></td></tr>' .
    '<tr><td style="padding:4px 12px 4px 0;color:#666">Email</td><td>' . ($email !== '' ? $esc($email) : '<i>(non renseigné)</i>') . '</td></tr>' .
    '<tr><td style="padding:4px 12px 4px 0;color:#666">Commune</td><td>' . ($commune !== '' ? $esc($commune) : '<i>(non renseignée)</i>') . '</td></tr>' .
    '<tr><td style="padding:4px 12px 4px 0;color:#666">Projet</td><td><b>' . $esc($sujet) . '</b></td></tr>' .
    '<tr><td style="padding:4px 12px 4px 0;color:#666">Reçu le</td><td>' . $esc($dateFr) . '</td></tr>' .
    '</table>' .
    '<p style="font-family:Arial,sans-serif;font-size:14px;margin:16px 0 4px;color:#666">Message :</p>' .
    '<div style="font-family:Arial,sans-serif;font-size:14px;white-space:pre-wrap;border-left:3px solid #c9a44a;padding-left:12px">' . $esc($message) . '</div>';

// 2) Accusé de réception (vers le prospect) — texte validé par le client
$ackPrenom = $nom !== '' ? $nom : 'Bonjour';
$ackText =
    "Bonjour $nom,\n\n" .
    "Merci pour votre message. Nous avons bien reçu votre demande concernant $sujet et nous vous en remercions.\n\n" .
    "Nous revenons vers vous sous 24-48h ouvrées pour échanger sur votre projet.\n\n" .
    "Pour toute urgence, vous pouvez nous joindre directement au 06 10 03 34 08.\n\n" .
    "À très vite,\n" .
    "L'équipe BM2R – Bâti Multi Rénovation\n\n" .
    "—\n" .
    "BM2R – Bâti Multi Rénovation\n" .
    "Artisan multi-rénovation · La Clayette (71800)\n" .
    "Tél : 06 10 03 34 08\n" .
    "Email : bm2rbatimulti@gmail.com\n" .
    "Web : https://bm2rbatimulti.fr\n";

$ackHtml =
    '<div style="font-family:Arial,sans-serif;font-size:15px;color:#1a1a1a;line-height:1.55">' .
    '<p>Bonjour ' . $esc($nom) . ',</p>' .
    '<p>Merci pour votre message. Nous avons bien reçu votre demande concernant <b>' . $esc($sujet) . '</b> et nous vous en remercions.</p>' .
    '<p>Nous revenons vers vous sous <b>24-48h ouvrées</b> pour échanger sur votre projet.</p>' .
    '<p>Pour toute urgence, vous pouvez nous joindre directement au <a href="tel:+33610033408">06 10 03 34 08</a>.</p>' .
    '<p>À très vite,<br>L\'équipe BM2R – Bâti Multi Rénovation</p>' .
    '<hr style="border:none;border-top:1px solid #e0e0e0;margin:18px 0">' .
    '<p style="font-size:13px;color:#666;margin:0">' .
    '<b>BM2R – Bâti Multi Rénovation</b><br>' .
    'Artisan multi-rénovation · La Clayette (71800)<br>' .
    'Tél : 06 10 03 34 08<br>' .
    'Email : <a href="mailto:bm2rbatimulti@gmail.com">bm2rbatimulti@gmail.com</a><br>' .
    'Web : <a href="https://bm2rbatimulti.fr">https://bm2rbatimulti.fr</a>' .
    '</p></div>';

// --- Envoi -----------------------------------------------------------------
try {
    // 1. Mail interne
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $cfg['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $cfg['smtp_user'];
    $mail->Password   = $cfg['smtp_pass'];
    $mail->SMTPSecure = $cfg['smtp_secure'];
    $mail->Port       = (int) $cfg['smtp_port'];
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($cfg['from_email'], $cfg['from_name']);
    $mail->addAddress($cfg['to_email'], $cfg['to_name']);
    if ($email !== '') {
        $mail->addReplyTo($email, $nom); // Francis répond directement au prospect
    }
    $mail->Subject = "Demande de devis – $sujet" . ($commune !== '' ? " ($commune)" : '');
    $mail->isHTML(true);
    $mail->Body    = $adminHtml;
    $mail->AltBody = $adminText;
    $mail->send();

    // 2. Accusé de réception au prospect (uniquement si email fourni)
    if ($email !== '') {
        $ack = new PHPMailer(true);
        $ack->isSMTP();
        $ack->Host       = $cfg['smtp_host'];
        $ack->SMTPAuth   = true;
        $ack->Username   = $cfg['smtp_user'];
        $ack->Password   = $cfg['smtp_pass'];
        $ack->SMTPSecure = $cfg['smtp_secure'];
        $ack->Port       = (int) $cfg['smtp_port'];
        $ack->CharSet    = 'UTF-8';

        $ack->setFrom($cfg['from_email'], 'BM2R – Bâti Multi Rénovation');
        $ack->addAddress($email, $nom);
        $ack->addReplyTo($cfg['to_email'], 'BM2R');
        $ack->Subject = 'Votre demande de devis bien reçue – BM2R';
        $ack->isHTML(true);
        $ack->Body    = $ackHtml;
        $ack->AltBody = $ackText;
        $ack->send();
    }
} catch (Exception $e) {
    http_response_code(500);
    respond(false, "L'envoi a échoué. Merci de réessayer ou de nous appeler au 06 10 03 34 08.");
}

respond(true);
