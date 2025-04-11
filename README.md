# DatAdrie

Projet Symfony avec une page de connexion.

## Prérequis
- PHP 8.x
- Composer
- Symfony CLI
- MySQL (phpMyAdmin)

## Installation
1. Clonez le dépôt :
   ```bash
   git clone https://github.com/alphandamedee/DatAdrie.git
   cd DatAdrie

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