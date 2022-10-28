# ECF Studi

## Projet pour l'Evaluation Continue de Formation finale Studi : développement d'une 'API Salle de sport'

### Prérequis

- Avoir PHP >= 8.1.9 installé sur la machine (ZIP téléchargeable sur php.net)
    - l'extension `mysqli` doit être présente et activée
    - php.exe doit être présent dans le PATH
- Avoir la cmdlet `symfony` présente sur la machine
    - peut être installé via l'outil `scoop` : lancer un terminal Powershell et taper `iex (new-object net.webclient).downloadstring('https://get.scoop.sh')`
    - Puis taper `scoop install symfony-cli` pour installer le CLI `symfony`
- Avoir une instance MariaDB fonctionnel sur le PC

### Installation

Après avoir cloné le projet, ouvrir le projet via Visual Studio Code par exemple et lancer un terminal, puis taper la commande `composer install` pour installer toutes les dépendances du projet.

Changer les variables contenues dans le fichier `.env` à la racine du projet pour ce qui concerne l'accès à la base de données.

### Exécution

- Créer la base de données via la commande `php bin/console doctrine:database:create`.
- Effectuer les migrations via les commandes `php bin/console make:migration` et `php bin/console doctrine:migrations:migrate`.
- Taper ensuite la commande `symfony server:start` pour lancer le serveur Web qui sera disponible à l'adresse `https://127.0.0.1:8000`.

### Déploiement

Ce site a été déployé via Heroku à l'adresse suivante : `https://fjfitness.herokuapp.com/`
