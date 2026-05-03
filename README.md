# MYstage — Plateforme de gestion des stages CY Tech

## Installation

### Prérequis
Installer Docker et Docker Compose :
```bash
sudo apt install docker docker-compose
```

### Cloner le projet
```bash
git clone https://github.com/Irissssssss24/DevWeb.git
cd DevWeb
```

### Configurer l'environnement
```bash
cp .env.example .env
```

### Lancer Docker
```bash
docker-compose up -d --build
```

### Installer les dépendances
```bash
docker-compose exec app composer install
```

### Générer la clé de l'application
```bash
docker-compose exec app php artisan key:generate
```

### Créer les tables et les données de test
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

---

## Accès au projet

- Application : http://localhost:8000
- Boîte mail de test (MailHog) : http://localhost:8025

---

## Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Superuser (tous les rôles) | superuser@gmail.com | superuser |
| Étudiant | etudiant@test.com | mdp_test |
| Entreprise | entreprise@test.com | mdp_test |
| Tuteur | tuteur@test.com | mdp_test |
| Administrateur | admin@test.com | mdp_test |

> Le compte **superuser** donne accès à tous les rôles — utile pour tester toutes les fonctionnalités sans changer de compte.

---

## Problèmes fréquents

### Port 5432 déjà utilisé
Si PostgreSQL est déjà installé sur votre machine, changez le port dans `docker-compose.yml` :
```yaml
ports:
  - "5433:5432"  # remplacer 5433 par n'importe quel port libre
```

### Page blanche ou erreur 500
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

### Remettre la base à zéro
```bash
docker-compose exec app php artisan migrate:fresh --seed
```