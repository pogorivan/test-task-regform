Для запуска приложения:
+ запускаем в консоли из папки приложения `docker-compose up`
+ выполняем в консоли `docker exec -i -t <id> /bin/bash`, где `<id>` - идентификатор контенера php-fpm (можно узнать, выполнив `docker ps`), далее в контейнере выполняем `composer install`
+ запускаем в браузере http://localhost:9909/migrate.php для создания структуры БД
+ переходим http://localhost:9909/