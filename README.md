Comanndos
sacar reporte de calidad de covertura:
```
./vendor/bin/phpunit --coverage-text (en consola)

./vendor/bin/phpunit --coverage-html coverage (en reporte html)

composer dump-autoload
```

sacar bdd:
```
./vendor/bin/behat --format=pretty --out=std --format=html --out=behat.html
./vendor/bin/behat
```


composer require --dev behat/mink-extension:^2.3.1 --with-all-dependencies --ignore-platform-reqs


Pruebas de interfaz de usuario:
```
Instalacion de dependencias:
composer require --dev behat/mink-extension
composer require --dev behat/mink-selenium2-driver
composer require --dev dmore/chrome-mink-driver

Ejecucion de pruebas:
vendor/bin/phpunit tests/UI/Pages/LoginPageTest.php
vendor/bin/phpunit tests/UI/Pages/AdminLoginTest.php
```

Antes de Construir la imagen de docker, es necesario ejecutar el comando:
```
docker network create tu_red_docker
```
despues 
```
docker compose up -d
```
