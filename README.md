<div style="text-align: center;">
  <img src="https://actus.ulb.be/medias/photo/logo-universite-libre-bruxelles_1661952138925-png?ID_FICHE=19524" alt="ULB Logo" />
</div>

# Infofin

Infofin est une application web qui sert de plateforme centralisée pour les appels à projets, financements, et séances
d'information, facilitant la recherche d'opportunités pour les chercheurs.

## Prérequis

Pour faire fonctionner Infofin, vous avez besoin de :

- PHP 8.x
- Composer
- Node.js
- NPM
- Une base de données MariaDB

## Installation

Pour installer et lancer l'application Infofin, suivez ces étapes :

1. Clonez le répertoire du projet :

   ```bash
   git clone <url_du_depot>
   cd infofin
   ```

2. Installez les dépendances PHP avec Composer :

   ```bash
   composer install
   ```

3. Installez les dépendances JavaScript avec NPM :

   ```bash
   npm install
   ```

4. Compilez les assets front-end :

   ```bash
   npm run dev
   ```

5. Générez le fichier `.env` à partir du fichier d'exemple :

   ```bash
   cp .env.example .env
   ```

6. Générez la clé de l'application :

   ```bash
   php artisan key:generate
   ```

7. Créez la base de données et appliquez les migrations :

   ```bash
   php artisan migrate
   ```

8. Démarrez le serveur de développement :

   ```bash
   php artisan serve
   ```

L'application devrait maintenant être accessible sur `http://localhost:8000`.

## Fonctionnalités

- Base de données centralisée des appels à projets, financements, et séances d'informations.
- Outil de recherche intuitif permettant aux chercheurs de trouver les opportunités qui leur correspondent.

## Auteurs

- Guillaume Stordeur
- Axel Hoffmann

## Licence

Copyright © ULB


