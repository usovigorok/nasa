NASA project.
====

Used technologies:

1. Symfony framework 3.
2. MySQL and Doctrine.

Please do the following before start:

1. Run composer install.
2. Check MySQL user and password on your machine. I use root as user, no password.
You can change user/password in parameters.yml.

Run these commands for database preparation:
php ./bin/console doctrine:database:create
php ./bin/console doctrine:schema:update --force

3. Run:
php bin/console server:run

After that local server will run.

Ok, let's see what we have:

1. Go to http://localhost:8000/
Here we get response {"hello":"world!"}.

This is managed by src/AppBundle/Controller/DefaultController.php.

2. Run command to request the data from the last 3 days from nasa api:
php bin/console app:get-nasa-data
After that data will appear in database.

Code is located in src/AppBundle/Command/GetNasaDataCommand.php

3. Go to http://localhost:8000/neo/hazardous
You will see all DB entries which contain potentially hazardous asteroids.

Code for this and following points is located in src/AppBundle/Controller/AsteroidController.php.

4. Go to http://localhost:8000/neo/fastest/true
You will see the model of the fastest hazardous asteroid.

Go to http://localhost:8000/neo/fastest/false
You will see the model of the fastest non-hazardous asteroid.

5. Go to http://localhost:8000/neo/best-year/true
You will see a year with most hazardous asteroids.

Go to http://localhost:8000/neo/best-year/false
You will see a year with most non-hazardous asteroids.

6. Go to http://localhost:8000/neo/best-month/true
You will see a month and year with most hazardous asteroids.

Go to http://localhost:8000/neo/best-month/false
You will see a month and year with most non-hazardous asteroids.

Thanks.