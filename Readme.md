# Installer le projet

## Prérequis    

 - docker compose
 - git
 - docker
 - make

## Installation

1. Cloner le projet
```bash
git clone git@github.com:ibou/we-movies.git 
```

2. Se déplacer dans le dossier du projet
```bash
cd we-movies
```

3. Lancer le projet 
```bash
make init-project
```
> En commandes séparées en options
```bash
    make start
    make composer-install 
    make yarn-install-and-run 
    make launch
```

> Le projet est maintenant installé et accessible à l'adresse suivante : http://localhost:8082

## Liste des container existants

- php 8.3
- nginx
- node 20

## Commandes utiles

- Lancer le projet
```bash
make 
```

- Arrêter le projet
```bash
make stop
```

- Se connecter au container php
```bash
make shell-php
```

- Se connecter au container node
```bash
make shell-node
```

- Redeemarrer les containers
```bash
make restart
```

- Lancer les tests avec la commande make test
```bash
make test
```

