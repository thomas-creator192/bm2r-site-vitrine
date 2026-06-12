# BM2R — Site vitrine

**Client :** BM2R (Bâti Multi Rénovation), Francis Roy
**Lieu :** La Clayette (71800), Brionnais / Saône-et-Loire
**Type :** Site vitrine statique multi-pages (HTML / CSS / JS)
**Date :** Juin 2026

## Contexte

Artisan multi-rénovation. Différenciateur central : un seul interlocuteur pour
tous les corps de métier. Cible : particuliers en rénovation, zone rurale dans
un rayon de 50 km autour de La Clayette (Gibles, Chauffailles, Baudemont,
St-Symphorien-des-Bois).

Contact affiché sur le site : 06 10 03 34 08 / bm2rbatimulti@gmail.com

## Structure

- `index.html` — accueil
- Pages métier : `electricite`, `plomberie`, `placo-isolation`, `chauffage`,
  `peinture`, `menuiserie`
- `salle-de-bain.html` — offre salle de bain clé en main
- `realisations.html` — galerie avant/après
- `zone-intervention.html` — secteur géographique
- `contact.html` — coordonnées et formulaire
- `blog.html` + 3 articles SEO
- `styles.css` — design system (palette RAL 6009 vert sapin + RAL 1015 ivoire)
- `script.js` — interactions (menu, accordéon FAQ)
- `photos.js` — chargement des photos (Unsplash, repli automatique)
- `assets/logo-bm2r.png` — logo

## Déploiement

Site 100 % statique, aucun back-end requis.

1. **Prévisualisation locale :** ouvrir `index.html` dans un navigateur.
2. **Mise en ligne (gratuit) :** glisser-déposer ce dossier sur Netlify ou
   Cloudflare Pages. HTTPS inclus, URL en 2 minutes.
3. **Domaine :** brancher un nom de domaine (ex. bm2r-renovation.fr) une fois
   le contenu validé par le client.

## À faire avant publication

- Validation du contenu par Francis Roy
- Remplacer les photos Unsplash par de vraies photos de chantiers BM2R
- Vérifier / brancher le formulaire de contact (envoi d'email)
