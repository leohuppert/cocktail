# Projet Web L3 INFO 

## Gestion de cocktails

[![Build Status](https://travis-ci.com/leohuppert/cocktail.svg?token=CDJVs9W9oD9aREdu5nHQ&branch=master)](https://travis-ci.com/leohuppert/cocktail)

### Lancer le projet

````
composer install
bower install
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load
php bin/console server:run
````

need : php, mariadb, composer, bower