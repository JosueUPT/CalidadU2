Comanndos
sacar reporte de calidad de covertura:
```
./vendor/bin/phpunit --coverage-text (en consola)

./vendor/bin/phpunit --coverage-html coverage (en reporte html)

composer dump-autoload
```

sacar bdd:
```
composer require --dev emuse/behat-html-formatter

vendor/bin/behat --format pretty --format html --out std --out reports  (reporte en html)
./vendor/bin/behat (reporte en consola)
```


composer require --dev behat/mink-extension:^2.3.1 --with-all-dependencies --ignore-platform-reqs


Pruebas de interfaz de usuario:
```
Instalacion de dependencias:
composer require --dev behat/mink-extension
composer require --dev behat/mink-selenium2-driver
composer require --dev dmore/chrome-mink-driver

Ejecucion de pruebas UI:
vendor/bin/phpunit tests/UI/Pages/LoginPageTest.php
vendor/bin/phpunit tests/UI/Pages/AdminLoginTest.php
vendor/bin/phpunit tests/UI/Pages/RegisterPageTest.php
vendor/bin/phpunit tests/UI/Pages/SearchPageTest.php
vendor/bin/phpunit tests/UI/Pages/ShopPageTest.php
vendor/bin/phpunit tests/UI/Pages/ContactPageTest.php

vendor/bin/phpunit tests/UI/Pages/AdminProductTest.php
vendor/bin/phpunit tests/UI/Pages/AdminOrderTest.php
vendor/bin/phpunit tests/UI/Pages/AdminUsersMessagesTest.php

```

Antes de Construir la imagen de docker, es necesario ejecutar el comando:
```
docker network create tu_red_docker
```
despues 
```
docker compose up -d
```
