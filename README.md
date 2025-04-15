# ADRIEDATA

Application de gestion d'inventaire et suivi d'interventions sur matériel informatique développée avec Symfony.

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- MySQL ou MariaDB
- Navigateur web moderne

## Installation

1. **Clonez le dépôt :**
   ```bash
   git clone https://github.com/alphandamedee/adriedata25.git
   cd adriedata25

2. Installez les dépendances :
bash

Collapse

Unwrap

Copy
composer install

3. Configurez la base de données dans .env.local :
text

Collapse

Wrap

Copy
DATABASE_URL="mysql://root:@127.0.0.1:3306/adriedata?serverVersion=8.0"

4. Mettez à jour la base de données :
bash

Collapse

Wrap

Copy
php bin/console doctrine:migrations:migrate

5. Lancez le serveur :
bash

Collapse

Wrap

Copy
symfony server:start

# Fonctionnalités
Gestion des utilisateurs : Authentification et gestion des profils
Gestion d'inventaire : Ajout, modification et suppression de matériel
Suivi d'interventions : Suivi des opérations de maintenance
Tableau de bord : Statistiques sur les interventions

# Structure du projet
* src/Controller/ : Contrôleurs de l'application
* src/Entity/ : Entités Doctrine (modèles)
* src/Repository/ : Repositories pour les requêtes à la base de données
* templates/ : Vues Twig de l'application
* public/ : Ressources accessibles publiquement (CSS, JavaScript, images)

# Branches Git
°main : Version stable
°dev : Branche de développement

# Licence
Ce projet est développé par Adrie et est sous licence privée.

# Contact
Pour toute question ou suggestion, veuillez contacter :

Email : djouneid.adrie@gmail.com 