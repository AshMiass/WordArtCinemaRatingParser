# WordArtCinemaRatingParser

###Для старта:###
1) Внести изменения (user, path, host) в config\db.conf.php
2) Дать права этому пользователю на базу 
3) Запустить php src\entry.php --init-db

Парсинг:
php src\entry.php

Парсинг с продолжением с прерваной раннее страницы:
php src\entry.php --continue

Почистить дирикторию с кешем:
php src\entry.php --cc

Фронт:
1) запустить php -S 127.0.0.1:8080 -t ./src/
2) открыть в браузере 127.0.0.1:8080/index.html

##
##
##

Использовалось:
* PHP 7.4.5
* MySQL 5.6.47
* Code editor: Visual Studio Code

Схема базы лежит в db_schema.JPG и \config\init.sql

Затрачено времени: 25 часов