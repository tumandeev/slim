# Mpstats product parser

Для работы на машине требуется Docker и Docker-compose

### Установка
Из корня проекта выполнить:
```sh
docker-compose up -d
docker-compose run composer install

```

Если все установлено верно, то можно будет работать с проектом:
```
Выполнение Миграций         - http://localhost:8000/migration/migrate
Сбор товаров                - http://localhost:8000/product/parse
Получение последнего обхода - http://localhost:8000/product/get?q=джинсы
```