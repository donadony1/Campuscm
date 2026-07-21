# CampusCM — SaaS de vitrines pour écoles de formation au Cameroun

Application PHP procédural + PDO + Bootstrap 5 + jQuery. Testée avec succès
sur PHP 8.3 (serveur intégré) **et** Apache (avec `.htaccess`, conditions
proches d'un hébergement mutualisé réel).

## Fonctionnalités incluses

- **Site public** : accueil, recherche/filtres (ville, domaine, mot-clé),
  vitrine détaillée par école (filières, galerie photo, avis visiteurs,
  bouton WhatsApp).
- **Inscription école** en libre-service (créée en statut "en attente").
- **Dashboard admin école** : édition du profil (logo, couverture,
  description, contact), gestion des filières (CRUD), galerie photo
  (upload + suppression AJAX).
- **Back-office super-admin** : vue d'ensemble, validation/rejet/suppression
  des écoles.
- Sécurité : PDO + requêtes préparées partout, hash bcrypt des mots de
  passe, jetons CSRF sur tous les formulaires, vérification systématique
  que chaque admin ne peut modifier que les données de SA propre école,
  upload d'image validé par type MIME réel (pas juste l'extension).

## Démarrage rapide (local, sans MySQL)

Par défaut le projet utilise **SQLite** (fichier unique, zéro
configuration) — idéal pour tester avant de déployer.

```bash
php -S localhost:8000
```

Puis ouvrez `http://localhost:8000`. La base et le compte super-admin sont
créés automatiquement au premier chargement.

**Compte super-admin par défaut :**
- email : `admin@campuscm.cm`
- mot de passe : `admin123`
- ⚠️ à changer immédiatement en production (directement en base, colonne
  `mot_de_passe`, avec un hash généré par `password_hash()`).

## Déploiement en production (hébergement mutualisé / cPanel)

1. **Uploadez tout le contenu de ce dossier** à la racine de votre
   `public_html` (ou du sous-dossier de votre domaine). La structure a été
   pensée pour un webroot unique : `includes/` et `data/` restent protégés
   par leurs propres `.htaccess` (`Require all denied`), tout le reste est
   public.
2. **Créez une base MySQL** depuis cPanel, puis importez
   `database/schema_mysql.sql` via phpMyAdmin.
3. **Modifiez `includes/config.php`** :
   ```php
   define('DB_DRIVER', 'mysql');
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'voscompte_campuscm');
   define('DB_USER', 'voscompte_user');
   define('DB_PASS', 'votre_mot_de_passe');
   define('APP_URL', 'https://votredomaine.cm');
   define('DEBUG_MODE', false);
   ```
4. **Permissions** : assurez-vous que `assets/uploads/logos`,
   `assets/uploads/covers`, `assets/uploads/photos` et `data/` (si vous
   restez en SQLite) sont accessibles en écriture par PHP (chmod 755,
   ou 775 selon votre hébergeur).
5. Changez le mot de passe du compte super-admin par défaut.

### Vérifications de sécurité effectuées

- `includes/*.php` et `data/*.sqlite` → testés en 403 Forbidden via Apache
  réel avec `.htaccess`.
- URLs propres `/ecole/mon-institut` → testées fonctionnelles via
  `mod_rewrite`.
- Accès aux dashboards sans connexion → redirection 302 vers `login.php`
  vérifiée.

## Structure du projet

```
index.php, ecole.php, recherche.php,      → pages publiques
login.php, register.php, logout.php
assets/                                    → CSS, JS, uploads
admin/pages/                               → dashboard admin école (rôle admin_ecole)
admin/processing/                          → la logique des pages
superadmin/                                → back-office plateforme (rôle super_admin)
includes/                                  → config, connexion DB, auth, fonctions (protégé)
database/schema_mysql.sql                  → schéma à importer en production
data/                                      → fichier SQLite (mode démo, protégé)
```

## Paiement Mobile Money (Notch Pay) — Plan Vitrine Premium

Un plan payant "Vitrine Premium" (badge Vérifié + mise en avant, 5 000 FCFA/mois
par défaut) est intégré via **Notch Pay** (MTN Mobile Money, Orange Money, carte).

### Configuration

1. Créez un compte sur [business.notchpay.co](https://business.notchpay.co) et
   récupérez vos clés (Développeurs > Clés API) :
   - clé publique sandbox `sb.xxxxx` pour tester, clé live `b.xxxxx` en prod.
2. Renseignez-les dans `includes/config.php` :
   ```php
   define('NOTCHPAY_PUBLIC_KEY', 'b.votre_cle_ici');
   define('NOTCHPAY_WEBHOOK_HASH', 'votre_hash_webhook');
   ```
3. Dans le dashboard Notch Pay, section **Webhooks**, ajoutez l'URL :
   `https://votredomaine.cm/webhook-notchpay.php` (événements `payment.complete`
   et `payment.failed`). Copiez le "Hash Key" affiché dans `NOTCHPAY_WEBHOOK_HASH`.
4. Vérifiez que l'extension **php-curl** est activée chez votre hébergeur
   (standard sur cPanel, mais à confirmer dans "PHP Selector").

### Comment ça marche

- L'école clique "Passer au Premium" dans son dashboard → redirection vers
  Notch Pay (choix MTN/Orange/carte).
- **Double confirmation** (bonne pratique) : au retour, `admin/paiement-retour.php`
  revérifie le statut réel auprès de l'API (jamais confiance au seul retour
  navigateur) ; en parallèle, `webhook-notchpay.php` reçoit une confirmation
  serveur-à-serveur signée (HMAC-SHA256), ce qui fonctionne même si l'utilisateur
  ferme l'onglet avant la fin du paiement Mobile Money.
- Le plan premium est activé 30 jours, prolongeable, avec rétrogradation
  automatique à l'expiration (aucune tâche cron requise).
- Testé de bout en bout : initiation de paiement, échec géré proprement,
  webhook signé → activation automatique, badge affiché en public.

### Changer le tarif

Modifiez `PLAN_PREMIUM_PRIX_MENSUEL` et `PLAN_PREMIUM_DUREE_JOURS` dans
`includes/config.php`.

## Limites connues de cette version (MVP)

- Pas d'envoi d'email automatique (confirmation d'inscription, notification
  de validation) — à ajouter via PHPMailer + SMTP si besoin.
- Pas de pagination sur la recherche (à ajouter si le nombre d'écoles
  dépasse plusieurs dizaines).
- Un seul compte admin par école (pas de gestion multi-utilisateurs par
  établissement).
