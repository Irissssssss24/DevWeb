# Installer docker
```bash
sudo apt install docker 
```


# Installer docker-compose
```bash
sudo apt install docker-compose
```


# Cloner le Projet 
```bash
git clone https://github.com/Irissssssss24/DevWeb.git
cd DevWeb
```


# Copier le fichier d'environnement
```bash
cp .env.example .env
```

# Lancer le Docker 
```bash
docker-compose up -d --build 
```

# Installer composer dans les contenaires
```bash 
docker-compose exec app composer install
```

# Générer la clé de l'application 
```bash 
docker-compose exec app php artisan key:generate
```

# Créer les tables et les données tests
```bash
docker-compose exec app php artisan migrate:fresh --seed
```


# Accéder au projet 

Le projet est accesible sur http://localhost:8000

# Connexion aux mails

aller sur http://localhost:8025 pour accéder au mail de vérification

# Problème de port

si il y a un problème de port (ports 5432 already use) 
il faut changer le port utiliser (le port est déjà utiliser)

```YAML 
/docker-compose.yml 
ports:
      - "5433:5432" -- changer cette ligne en mettant le port 5433 côté machine ou plus (jusqu'à trouver un port libre)
```



