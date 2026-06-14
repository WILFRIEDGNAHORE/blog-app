# Blog App — Fullstack Laravel + React

Projet d'apprentissage Fullstack avec Laravel, React, MySQL, Nginx et Docker.

---

## Technologies

| Technologie | Rôle |
|---|---|
| Laravel | API REST |
| React + Vite | Frontend |
| MySQL | Base de données |
| Nginx | Serveur web |
| Docker | Conteneurisation |
| GitHub Actions | CI / Tests automatisés |

---

## Fonctionnalités

- Articles avec image, tags et statut (brouillon/publié)
- Commentaires sur les articles
- Tags réutilisables (relation Many-to-Many)
- Upload et prévisualisation d'images
- Validation frontend (Zod) et backend (Form Requests)
- Autorisation par Policy
- Architecture en Services

---

## Architecture

```text
Navigateur
    │
    ▼
React (http://localhost:5173)
    │ HTTP
    ▼
Laravel API (http://localhost:8000)
    │
    ▼
MySQL
```

### Architecture Backend

```text
Controller
    ↓
Form Request (validation)
    ↓
Policy (autorisation)
    ↓
Service (logique métier)
    ↓
Model
```

---

## Lancer le projet

### Cloner le projet

```bash
git clone <repository-url>
cd blog-app
```

### Démarrer les containers

```bash
docker compose up -d
```

### Configurer Laravel

```bash
docker exec -it blog-app cp .env.example .env
docker exec -it blog-app php artisan key:generate
docker exec -it blog-app php artisan migrate
docker exec -it blog-app php artisan storage:link
```

### Créer un utilisateur de test

```bash
docker exec -it blog-app php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@blog.com',
    'password' => bcrypt('password'),
]);
```

### Accès

| Service | URL |
|---|---|
| React | http://localhost:5173 |
| Laravel API | http://localhost:8000 |
| phpMyAdmin | http://localhost:8080 |

---

## API Endpoints

### Articles

| Méthode | URL | Description |
|---|---|---|
| GET | /api/articles | Lister les articles (paginés) |
| POST | /api/articles | Créer un article |
| GET | /api/articles/{id} | Afficher un article |
| PUT | /api/articles/{id} | Modifier un article |
| DELETE | /api/articles/{id} | Supprimer un article |

### Commentaires

| Méthode | URL | Description |
|---|---|---|
| GET | /api/articles/{id}/comments | Lister les commentaires |
| POST | /api/articles/{id}/comments | Ajouter un commentaire |
| DELETE | /api/articles/{id}/comments/{commentId} | Supprimer un commentaire |

### Tags

| Méthode | URL | Description |
|---|---|---|
| GET | /api/tags | Lister les tags |
| POST | /api/tags | Créer un tag |
| DELETE | /api/tags/{id} | Supprimer un tag |

---

## Tests

```bash
docker exec -it blog-app php artisan test
```

---

## Connexion MySQL

| Champ | Valeur |
|---|---|
| Base | blog_db |
| Utilisateur | blog_user |
| Mot de passe | blog_pass |