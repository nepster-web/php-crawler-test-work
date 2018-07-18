# PHP Crawler
Приложение демонстрирует пример работы бота-парсера, 
подробное описание которого описано в [тестовом задании](./TASK.md).


# Запуск 
Для работы приложения необходимо установить 
[Docker](https://www.docker.com) и 
[Docker Compose](https://docs.docker.com/compose/). 

Запуск приложения осуществляется с помощью makefile:

```
make cwr ARGS="-u=http://robotstxt.org.ru -d=2"
```

или

```
make cwr ARGS="--url==http://robotstxt.org.ru --depth=2"
```

Результат сохраняется в директорию  **www/reports**.


# Тесты 
Приложение использует [PHPUnit](https://phpunit.de/) для тестирования. 
Команда для запуска тестов:

```
make tests
```