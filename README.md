# Installer docker
'''bash
sudo apt install docker 
'''

# Installer docker-compose
'''bash
sudo apt install docker-compose
'''

# Cloner le Projet 
'''bash
git clone https://github.com/Irissssssss24/DevWeb.git
cd DevWeb
'''

# Copier le fichier d'environnement
'''bash
cp .env.example .env
'''

# Lancer le Docker 
'''bash
docker-compose up -d --build 
'''

# Générer la clé de l'application 
'''bash 
docker-compose exec app php artisan key:generate
'''

# Créer les tables et les données tests
'''bash
docker-compose exec app php artisan migrate --seed
'''

# Accéder au projet 

Le projet est accesible sur http://localhost:8000

