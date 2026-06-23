<?php
/**
 * config.sample.php — MODÈLE de configuration du formulaire de contact BM2R
 * ---------------------------------------------------------------------------
 * NE PAS mettre les vrais identifiants ici (ce fichier est versionné sur GitHub).
 *
 * Procédure :
 *   1. Sur o2switch (cPanel > Gestionnaire de fichiers, dossier public_html),
 *      copier ce fichier en  config.php
 *   2. Remplir config.php avec les vraies valeurs ci-dessous.
 *   3. config.php reste UNIQUEMENT sur le serveur (jamais commité, jamais écrasé
 *      par deploy.ps1 puisqu'il n'existe pas en local).
 */

return [

    // --- Boîte mail expéditrice (SMTP authentifié o2switch) -----------------
    // Créer la boîte dans cPanel > Comptes de messagerie, ex : contact@bm2rbatimulti.fr
    'smtp_host'   => 'mail.bm2rbatimulti.fr', // serveur sortant o2switch
    'smtp_port'   => 465,                      // 465 = SSL (recommandé) ; 587 = TLS
    'smtp_secure' => 'ssl',                    // 'ssl' pour 465, 'tls' pour 587
    'smtp_user'   => 'contact@bm2rbatimulti.fr',
    'smtp_pass'   => 'MOT_DE_PASSE_DE_LA_BOITE',

    // Adresse affichée comme expéditeur (doit être du domaine, sinon spam)
    'from_email'  => 'contact@bm2rbatimulti.fr',
    'from_name'   => 'Site BM2R',

    // --- Destinataire des demandes de devis ---------------------------------
    'to_email'    => 'bm2rbatimulti@gmail.com',
    'to_name'     => 'BM2R',

    // --- reCAPTCHA v3 -------------------------------------------------------
    // Clés à créer sur https://www.google.com/recaptcha/admin (type v3)
    // Laisser la valeur par défaut ci-dessous DÉSACTIVE la vérification reCAPTCHA
    // (le honeypot reste actif). Renseigner la vraie clé secrète pour l'activer.
    'recaptcha_secret'    => 'CLE_SECRETE_RECAPTCHA',
    'recaptcha_min_score' => 0.5, // 0.0 (laxiste) à 1.0 (strict) ; 0.5 = bon défaut
];
