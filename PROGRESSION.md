# PROGRESSION — Gestion Interventions ATECH

## 📦 Environnement

### PHP
- **Installation** : PHP 8.5.5 (ZTS Visual C++ 2022 x64)
- **Chemin** : `C:\php-8.5.5\php-8.5.5-Win32-vs17-x64\`
- **Extensions activées** : `pdo_sqlite`, `sqlite3` (étaient commentées dans php.ini)

### PHP Switcher
- **Système de bascule entre PHP 8.2 (XAMPP) et PHP 8.5.5**
- **Scripts** : `C:\php-switcher\`
  - `php.bat` — proxy qui redirige vers la version active
  - `active.txt` — contient `82` ou `85`
  - `switch-php82.bat` — bascule vers PHP 8.2
  - `switch-php85.bat` — bascule vers PHP 8.5.5

---

## 🚀 Projet Laravel

### Création (25/06/2026)
- **Commande** : `composer create-project laravel/laravel gestion-interventions`
- **Chemin** : `C:\Mansour_Extension\L3GLSIB\Projet&Memoire\gestion-interventions`
- **Laravel** : v13.17.0
- **PHP** : 8.5.5
- **Base de données** : SQLite (`database/database.sqlite`)

---

## 🎨 Installation de Breeze + Vue.js + Inertia

### Étapes
1. `composer require laravel/breeze --dev`
2. `php artisan breeze:install vue`

### Versions installées
| Technologie | Version |
|---|---|
| laravel/breeze | v2.4.2 |
| inertiajs/inertia-laravel | v2.0.24 |
| laravel/sanctum | v4.3.2 |
| tightenco/ziggy | v2.6.3 |
| Vue.js | v3.4.x |
| @inertiajs/vue3 | ^2.0.0 |
| Vite | v8.1.0 |
| Tailwind CSS | v3.2.x / v4.0.x |
| laravel-vite-plugin | ^3.1 |

### Correctifs appliqués
1. **`resources/js/bootstrap.js` manquant** — créé manuellement (non inclus dans Laravel 13)
2. **`package.json`** — scripts modifiés pour utiliser `node node_modules/vite/bin/vite.js build/dev` directement (contourne le bug du `&` dans le chemin `Projet&Memoire` qui cassait les `.cmd`)

### Composants générés
- Authentification complète (Login, Register, Password Reset, Email Verification)
- Pages : Dashboard, Welcome, Profile (Edit, Delete, Update Password)
- Layouts : Guest, Authenticated
- Components : TextInput, Checkbox, Modal, Dropdown, NavLink, etc.
- Middleware de rôles à implémenter

---

## ⚙️ Configuration PHP

### Extensions SQLite activées
- `extension=pdo_sqlite`
- `extension=sqlite3`

Ces deux lignes étaient commentées dans `php.ini` de PHP 8.5.5 (ligne 937 et 948).

---

---

## 🗄️ Backend — Base de données & Modèles

### Migrations créées
| Table | Colonnes |
|---|---|
| `users` | + `role`, `specialite`, `actif`, `avatar` (ajoutés) |
| `tickets` | ref, titre, description, priorite, categorie, statut, client_id, technicien_id, piece_jointe, deadline |
| `ticket_history` | ticket_id, user_id, action |
| `ticket_comments` | ticket_id, user_id, text, internal |
| `reports` | ticket_id, travaux, duree_heures, duree_minutes, materiel, observations, recommandations, signed_by |
| `evaluations` | ticket_id, client_id, note (1-5), commentaire |
| `notifications` | user_id, title, message, read, ticket_ref |

### Modèles Eloquent
- **User** — rôles (admin, technicien, client) + relations (tickets, notifications, évaluations)
- **Ticket** — relations client, technicien, history, comments, report, evaluation
- **TicketHistory** — belongsTo ticket, user
- **TicketComment** — belongsTo ticket, user (avec boolean `internal`)
- **Report** — belongsTo ticket
- **Evaluation** — belongsTo ticket, client
- **AppNotification** — belongsTo user

### Seeders
- **6 utilisateurs** : 1 admin (`nadia.k@atech-cyber.com / admin123`), 3 techniciens (Diarra/Réseau, Benamor/Matériel, El Fassi/Logiciel), 2 clients
- **10 tickets** avec historique détaillé
- **3 rapports** d'intervention
- **2 évaluations** (5★, 4★)
- **5 notifications**

---

## 🎯 Contrôleurs & Routes

### Middleware
- `RoleMiddleware` — filtre les routes par rôle (`role:admin`, `role:technicien`, `role:client`)

### Routes (Inertia)
| Méthode | URL | Controller | Rôle |
|---|---|---|---|
| GET | `/` | Landing page | Public |
| GET | `/dashboard` | DashboardController | Auth |
| GET | `/tickets` | TicketController@index | Auth |
| GET | `/tickets/create` | TicketController@create | Client |
| POST | `/tickets` | TicketController@store | Client |
| GET | `/tickets/{id}` | TicketController@show | Auth |
| PATCH | `/tickets/{id}/status` | TicketController@updateStatus | Tech/Admin |
| POST | `/tickets/{id}/assign` | TicketController@assign | Admin |
| POST | `/tickets/{id}/comments` | TicketController@addComment | Auth |
| GET | `/tickets/{id}/report/create` | TicketController@createReport | Tech |
| POST | `/tickets/{id}/report` | ReportController@store | Tech |
| POST | `/tickets/{id}/evaluate` | EvaluationController@store | Client |
| GET | `/admin/tickets` | AdminController@tickets | Admin |
| GET | `/admin/users` | AdminController@users | Admin |
| POST | `/admin/users/technician` | AdminController@storeTechnician | Admin |
| POST | `/admin/users/{id}/toggle` | AdminController@toggleUser | Admin |
| GET | `/admin/reviews` | AdminController@reviews | Admin |
| GET | `/notifications/count` | NotificationController@count | Auth |

---

## 🖼️ Frontend — Pages Vue

### Layouts
- **GuestLayout** — Landing, Login, Register
- **AppLayout** — Sidebar (indigo, 64), top bar avec recherche + notifications + profil

### Pages
| Page | Fichier | Rôle |
|---|---|---|
| Landing | `Pages/Landing.vue` | Public |
| Client Dashboard | `Pages/Client/Dashboard.vue` | Client |
| Tech Dashboard | `Pages/Tech/Dashboard.vue` | Technicien |
| Admin Dashboard | `Pages/Admin/Dashboard.vue` | Admin |
| Tickets list | `Pages/Ticket/Index.vue` | Tous |
| New ticket | `Pages/Ticket/Create.vue` | Client |
| Ticket detail | `Pages/Ticket/Show.vue` | Tous |
| Report form | `Pages/Ticket/ReportForm.vue` | Technicien |
| Admin Tickets | `Pages/Admin/Tickets.vue` | Admin |
| Admin Users | `Pages/Admin/Users.vue` | Admin |
| Admin Reviews | `Pages/Admin/Reviews.vue` | Admin |

### Comptes de démonstration
| Rôle | Email | Mot de passe |
|---|---|---|
| Admin | nadia.k@atech-cyber.com | admin123 |
| Technicien (Réseau) | a.diarra@atech-cyber.com | tech123 |
| Technicien (Matériel) | s.benamor@atech-cyber.com | tech123 |
| Technicien (Logiciel) | k.elfassi@atech-cyber.com | tech123 |
| Client | lea.morel@client.fr | client123 |
| Client | it@norda.io | client123 |

---

## 🔧 Correctifs appliqués
- `pdo_sqlite` et `sqlite3` activés dans php.ini de PHP 8.5.5
- `resources/js/bootstrap.js` créé (manquant dans Laravel 13)
- `package.json` : scripts modifiés pour utiliser `node node_modules/vite/bin/vite.js` (contourne bug `&` dans path)
- Middleware `role` alias enregistré dans `bootstrap/app.php`

---

## 📋 Reste à faire
- [ ] Génération PDF des rapports (Dompdf)
- [ ] Calendrier des interventions
- [ ] Pièces jointes (upload files)
- [ ] Export CSV
- [ ] Tests
