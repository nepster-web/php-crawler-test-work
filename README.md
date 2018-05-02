# PHP Crawler
Приложение демонстрирует пример работы бота-парсера, подробное описание которого описано в [тестовом задании](./TASK.md).


# Запуск 
Приложение использует [Docker](https://www.docker.com) и может быть запущено с помощью makefile:

```
make cwr ARGS="-u=http://robotstxt.org.ru -d=2"
make cwr ARGS="--url==http://robotstxt.org.ru --depth=2"
```

Результат сохраняется в директорию  **www/reports**