# Projet L3 INFO: Gestion de cocktails

### Lancer le projet

````
composer install
bower install
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console server:run
````

need : php, mariadb, composer, bower