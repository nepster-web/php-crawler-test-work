<?php

namespace src\interfaces;

/**
 * Crawler Interface
 * @package src\interfaces
 */
interface CrawlerInterface
{
    /**
     * События
     *  - Хит парсинга
     *  - Перед парсингом
     *  - После парсинга
     */
    const EVENT_HIT_CRAWL = 'event_hit_crawl';
    const EVENT_BEFORE_CRAWL = 'event_before_crawl';
    const EVENT_AFTER_CRAWL = 'event_after_crawl';

    /**
     * Запуск процесса
     *
     * @param $url
     * @param int $depth
     */
    public function crawl($url, $depth = 5);

    /**
     * Список просмотренных url страниц
     *
     * @return array
     */
    public function getSeen();

    /**
     * Обработчик событий
     *
     * @param $event
     * @param $cb
     * @throws \Exception
     */
    public function on($event, $cb);

    /**
     * Собрать URL на основе текущей страницы
     *
     * Примечание:
     * Данная функция охватывает множество вероятных
     * значений href, однако может быть несовершенной.
     *
     * @param string $href
     * @param string $currentUrl
     * @return bool|string
     */
    public function buildUrl($href, $currentUrl);

    /**
     * Собрать URL из массива на основе функции parse_url
     *
     * @param $parsed_url
     * @return string
     */
    public function unparseUrl(array $parsed_url);

    /**
     * Получить домен сайта из url
     *
     * @param $url
     * @return bool
     */
    public function getDomain($url);

    /**
     * Проверить работает ли url адрес
     *
     * @param $url
     * @return bool
     */
    public function isCorrectPage($url);

}