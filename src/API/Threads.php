<?php
/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2021 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP\API;

use DestyK\LztPHP\RequestException;

/**
 * Класс для взаимодействия с методами 'threads'
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-threadsgetlist
 *
 */
class Threads extends Init
{
    /**
     * Префикс URL для текущего класса
     *
     * @const string
     */
    const PREFIX_URI = 'threads/';

    /**
     * Выгружает список тем (с пагинацией).
     *
     * @param array $params Доступные опциональные параметры:
     *                      + forum_id           {string|number} Перечисление ID форумов (через запятую);
     *                      + thread_ids         {string|number} Перечисление ID тем (через запятую);
     *                      + creator_user_id    {number}        ID пользователя, создавшего темы;
     *                      + sticky             {boolean}       0 - вернуть закреплённые темы, 1 - незакреплённые;
     *                      + thread_prefix_id   {number}        Получить темы с указанными префиксом;
     *                      + thread_tag_id      {number}        Получить темы с указанным тегом;
     *                      + page               {number}        Получить указанную страницу с темами;
     *                      + limit              {number}        Получить указанное количество тем на странице;
     *                      + order              {string}        Сортировка тем.
     *                                                                         Доступны значения: natural, thread_create_date, thread_create_date_reverse,
     *                                                                                          thread_update_date, thread_update_date_reverse,
     *                                                                                          thread_view_count, thread_view_count_reverse, thread_post_count,
     *                                                                                          thread_post_count_reverse.
     *                      + thread_create_date {string}        Сортировка тем по дате создания.
     *                                                                         Доступны значения: thread_create_date, thread_create_date_reverse.
     *                      + thread_update_date {string}        Сортировка тем по дате обновления.
     *                                                                         Доступны значения: thread_create_date, thread_create_date_reverse.
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function getList(array $params = [])
    {
        return $this->requestBuilder(self::PREFIX_URI, parent::GET, $params);
    }

    /**
     * Выгружает информацию об указанной теме.
     *
     * @param int $threadId Идентификатор темы
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function aboutOne(int $threadId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $threadId . '/', parent::GET);
    }
}
