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
